<?php

/*
 * Classe moteur du CMS
 */

class Donkey extends Singleton
{
    // Classe config du système
    private $_sysConfig = NULL;
    // Tableau des modules chargés
    private $_modules   = array();
    
    protected function __construct($Loader)
    {
        $Loader->autoloads();
        
        // Chargement configuration système
        $this->_sysConfig = ConfigMgr::instance()->getConfig('sysConfig');
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
            $module->partialRender($OutputContent);

        echo $OutputContent;
    }
    
    public function runModule($module, $controller, $action)
    {
        $moduleDir = MODS_PATH . strtolower($module) . SEP;
        if(!is_dir($moduleDir))
        {
            throw new DkException('module.inexistant', $module);
        }
                
        // On détermine si une surcharge de la classe module est disponible
        $className = file_exists($moduleDir . ucfirst($module . MODULESUFFIX . EXT))
                        ? ucfirst($module . MODULESUFFIX) : 'Module';
        $moduleObj = new $className($module);
        $this->addModule($moduleObj);

        if(!is_subclass_of($moduleObj, 'Module'))
        {
            throw new DkException('class.should_unherit', 'Module ' . ucfirst($module . MODULESUFFIX), 'Module');
        }
        
        $moduleObj->run($controller, $action);
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
                if(strtoupper($module->moduleName()) == strtoupper($target))
                {
                    return $module;
                }
            }
        }
    }
    
    public function autoloads()
    {
        $autoloadConfig = ConfigMgr::instance()->getConfig('autoloadsConfig');
        foreach($autoloadConfig['helpers'] as $helper)
        {
            if(Loader::instance()->fileExists('inc/helpers/'. $helper . EXT))
                Loader::instance()->getFile('inc/helpers/' . $helper . EXT);
            else
                Loader::instance()->getFile('system/helpers/' . $helper . EXT);
        }
    }
}