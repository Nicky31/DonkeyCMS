<?php

/*
 * Classe moteur du CMS
 */

class Donkey extends Singleton
{
    // Classe config du système
    private $_sysConfig    = NULL;
    // Module maître ( = celui appelé en premier par l'url)
    private $_mainModule   = NULL;
    // Tableau des modules secondaires
    private $_minorModules = array();
    
    protected function __construct()
    {
        Loader::instance()->autoloads();
        
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
    }
    
    public function run()
    {
        // Traitement de la route
        $route = Router::getRouteArray(Router::getPathInfo());
        Router::setCurRouteParams($route['args']);
        define('MAIN_MODULE',     $route['module']);
        define('MAIN_CONTROLLER', $route['controller']);
        define('MAIN_ACTION',     $route['action']);

        // Lancement des modules
        $modulesList = Loader::instance()->getEnabledModules();
        foreach($modulesList as $curModule)
        {
            if($curModule['name'] == $route['module'])
                $this->_mainModule = $this->instanciateModule($route['module']);
            else
                $this->_minorModules[] = $this->instanciateModule($curModule);
        }
        // Dossier inexistant, module désactivé ou non indiqué dans donkey_modules :
        if($this->_mainModule == NULL) 
            throw new DkException('module.inexistant', $route['module']);
        assert('is_subclass_of($this->_mainModule, \'Module\') && \'Les classes modules doivent hériter de Module.\'');
        
        Hook::instance()->exec('pre_main_module');
        $this->_mainModule->run($route['controller'], $route['action']);
        $this->finalRender();
    }

    // Génère et envoit la page finale
    private function finalRender()
    {
        $OutputContent = $this->_mainModule->render();
        foreach($this->_minorModules as $module)
            $module->partialRender($OutputContent);

        echo $OutputContent;
    }
    
    /*
     * Instancie et retourne le module $module
     * Retourne NULL s'il est introuvable dans le dossier modules
     * Instancie la classe système Module ou sa surcharge si elle existe dans le dossier du module
     */
    private function instanciateModule($module)
    {
        $moduleDir = MODS_PATH . strtolower($module) . SEP;
        if(!is_dir($moduleDir))
            return NULL;

        $className = file_exists($moduleDir . ucfirst($module . MODULESUFFIX . EXT))
                        ? ucfirst($module . MODULESUFFIX) : 'Module';
        return new $className($module);
    }
    
    private function autoloads()
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