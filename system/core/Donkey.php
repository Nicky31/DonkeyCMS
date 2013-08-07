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
    
    protected function __construct($Loader)
    {
        $this->_loader = $Loader;
        
        // Config Manager
        $ConfigMgr = ConfigMgr::instance();
        
        // Chargement configuration système
        $this->_sysConfig = $ConfigMgr->loadConfig('inc/config/sys_config', 'sysConfig');
        $this->_sysConfig->toConstants();
        $this->autoloads();
        
        // Router
        Router::$_defaultRoute = array(
            'module'     => $this->_sysConfig['defaultModule'],
            'controller' => $this->_sysConfig['defaultController'],
            'action'     => $this->_sysConfig['defaultAction'],
            'args'       => array()
        );
        
        Input::init();
    }
    
    public function run()
    {
        $route = Router::getRouteArray(Router::getPathInfo());
        Router::setCurRouteParams($route['args']);
        define('MAIN_MODULE',     $route['module']);
        define('MAIN_CONTROLLER', $route['controller']);
        define('MAIN_ACTION',     $route['action']);

        $this->runModule($route['module'], $route['controller'], $route['action']);

        $this->finalRender();
    }

    // Génère et envoit la page finale
    public function finalRender()
    {
        $OutputContent = array_shift($this->_modules) -> render();
        foreach($this->_modules as $module)
            $module->partialRender();

        echo $OutputContent;
    }
    
    public function runModule($module, $controller, $action)
    {
        $moduleDir = MODS_PATH . strtolower($module) . SEP;
        if(!is_dir($moduleDir))
        {
            throw new DkException('module.inexistant', $module);
        }
        
        $params = array(
            'name' => $module,
            'controller' => $controller
        );
        $moduleObj = NULL;
        
        // Une surcharge de la classe module existe
        if(file_exists($moduleDir . ucfirst($module . MODULESUFFIX . EXT)))        
        {
            $className = ucfirst($module . MODULESUFFIX);
            $moduleObj = new $className($params);
        }
        else
        { 
            $moduleObj = new Module($params);
        }
        
        if(!is_subclass_of($moduleObj, 'Module'))
        {
            throw new DkException('class.should_unherit', 'Module ' . ucfirst($module . MODULESUFFIX), 'Module');
        }
        
        $moduleObj->run($action);
    }
    
    public function addModule($module)
    {
        $this->_modules[] = $module;
    }

    public function module($target = NULL)
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
        $autoloadConfig = ConfigMgr::instance()->loadConfig('inc/config/autoloads_config','autoloadsConfig');
        foreach($autoloadConfig['helpers'] as $helper)
        {
            if($this->_loader->fileExists('inc/helpers/'. $helper . EXT))
                $this->_loader->getFile('inc/helpers/' . $helper . EXT);
            else
                $this->_loader->getFile('system/helpers/' . $helper . EXT);
        }
    }
}