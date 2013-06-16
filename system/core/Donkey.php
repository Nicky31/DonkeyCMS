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

        $ConfigMgr =& $Loader->instanciate('system/core/config/ConfigMgr');
        
        $this->_sysConfig =& $ConfigMgr->loadConfig('inc/config/sys_config', 'sysConfig');
        $this->_sysConfig->toConstants();
        $this->autoloads();
    }
    
    public function run()
    {
        $this->_loader->getFile('system/core/module/Controller');
        $this->_loader->getFile('system/core/module/Module');
        
        $this->_output =& $this->_loader->instanciate('system/core/Output', $this);
        
        $route = $this->route();
        $this->runModule($route['module'], $route['controller'], $route['action']);
        
        $this->_output->render();
    }
    
    public function runModule($module,$controller,$action)
    {
        $moduleDir = MODS_PATH . $module . SEP;
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


    public function route()
    {
        $module = $this->_sysConfig['defaultModule'];
        $controller = $this->_sysConfig['defaultController'];
        $action = $this->_sysConfig['defaultAction'];
        
        if(!empty($_GET[$this->_sysConfig['routeGet']]))
        {
            $route = explode('/',$_GET[$this->_sysConfig['routeGet']]);
            
            if(!empty($route[0]))
                $module = $route[0];
            
            if(sizeof($route) > 1 && !empty($route[1]))
                $controller = $route[1];
           
            if(sizeof($route) > 2 && !empty($route[2]))
                $action = $route[2];
        }

        return array(
            'module' => $module,
            'controller' => $controller,
            'action' => $action
        );
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