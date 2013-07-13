<?php

/*
 * Classe moteur du CMS
 */

class Donkey extends Singleton
{
    // Instance du loader
    private $_loader    = NULL;
    // Classe config du système
    private $_sysConfig = NULL;
    // Tableau des modules chargés
    private $_modules   = array();
    // Classe Output qui gère la page finale
    private $_output    = NULL;
    
    protected function __construct(&$Loader)
    {
        $this->_loader =& $Loader;

        // Config Manager
        $ConfigMgr = $Loader->instanciate('system/core/config/ConfigMgr');
        
        // Chargement configuration système
        $this->_sysConfig =& $ConfigMgr->loadConfig('inc/config/sys_config', 'sysConfig');
        $this->_sysConfig->toConstants();
        $this->autoloads();
        
        // Router
        $Loader->getFile('system/core/Router');
        Router::$_defaultRoute = array(
            'module'     => $this->_sysConfig['defaultModule'],
            'controller' => $this->_sysConfig['defaultController'],
            'action'     => $this->_sysConfig['defaultAction'],
            'args'       => array()
        );
        
        $Loader->getFile('system/core/input/Input');
        Input::init();
    }
    
    public function run()
    {
        $this->_loader->getFile('system/core/module/Controller');
        $this->_loader->getFile('system/core/module/Module');
        
        $this->_output = $this->_loader->instanciate('system/core/Output', $this);
        
        $route = Router::getRouteArray(Router::getPathInfo());
        Router::setCurRouteParams($route['args']);
        $this->runModule($route['module'], $route['controller'], $route['action']);
        
        define('MAIN_MODULE',     $route['module']);
        define('MAIN_CONTROLLER', $route['controller']);
        define('MAIN_ACTION',     $route['action']);
        
        $this->_output->render();
    }
    
    public function runModule($module, $controller, $action)
    {
        $moduleDir = MODS_PATH . strtolower($module) . SEP;
        if(!is_dir($moduleDir))
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : Module <b>'. $module .'</b> inexistant !');
            return;
        }
        
        $params = array(
            'name' => $module,
            'controller' => $controller
        );
        $moduleObj = NULL;
        
        // Une surcharge de la classe module existe
        if(file_exists($moduleDir . ucfirst($module . MODULESUFFIX . EXT)))        
        {
            $moduleObj =& $this->_loader->instanciate('modules/' . $module . '/' . ucfirst($module . MODULESUFFIX), $params);
        }
        else
        { 
            $moduleObj =& $this->_loader->instanciate('Module', $params);
        }
        
        if($moduleObj instanceof Module === FALSE)
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : La classe module <b>'. $module . MODULESUFFIX .'</b> doit hériter de <b>Module</b> !');
            return;
        }
        
        $this->_modules[] =& $moduleObj;   
        $moduleObj->run($action);
    }
    
    public function &module($target = NULL)
    {
        if($target === NULL)
        {
            return $this->_modules;
        }
        else if(is_numeric($target))
        {
            return $this->_modules[$target];
        }
        else
        {
            foreach($this->_modules as $module)
            {
                if(strtoupper($module->name()) == strtoupper($target))
                {
                    return $module;
                }
            }
        }
    }
    
    public function autoloads()
    {
        $autoloadConfig =& ConfigMgr::instance()->loadConfig('inc/config/autoloads_config','autoloadsConfig');
        foreach($autoloadConfig['helpers'] as $helper)
        {
            $this->_loader->getFile('inc/helpers/' . $helper . EXT, FALSE, FALSE);
            $this->_loader->getFile('system/helpers/' . $helper . EXT, FALSE, FALSE);
        }
    }
    
}