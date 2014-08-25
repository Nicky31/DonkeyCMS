<?php

/*
 * Classe moteur du CMS
 */

class Donkey extends Singleton
{
    // Module maître ( = celui appelé en premier par l'url)
    private $_mainModule   = NULL;
    // Tableau des modules secondaires
    private $_minorModules = array();
    
    protected function __construct()
    {
        // Chargement de toutes les dépendances du système
        Loader::instance()->autoloads();
        $this->autoloads();

        $sysConfig = ConfigMgr::instance()->getConfig('sysConfig');
        define('DATASDONKEY', $sysConfig->item('datasDonkey'));
        define('URIEXT',      $sysConfig->item('uriExt'));
        
        // Router
        Router::$_defaultRoute = array(
            'module'     => $sysConfig['defaultModule'],
            'controller' => '',
            'action'     => '',
            'args'       => array()
        );
    }
    
    public function run()
    {
        // Traitement de la route
        $route = Router::getRouteArray(Router::getPathInfo());
        Router::setCurRouteParams($route['args']);

        // Lancement des modules
        $modulesList = PigRegistry::get_instance()->get('donkey.modulesModel')->allModules(TRUE);
        foreach($modulesList as $curModule)
        {
            if($curModule['name'] == $route['module'])
                $this->_mainModule = $this->instanciateModule($curModule);
            else
                $this->_minorModules[] = $this->instanciateModule($curModule);
        }
        // Dossier inexistant, module désactivé ou non indiqué dans donkey_modules :
        if($this->_mainModule == NULL) 
            throw new DkException('module.inexistant', $route['module']);
        assert('is_subclass_of($this->_mainModule, \'Module\') && \'Les classes modules doivent hériter de Module.\'');
        
        define('MAIN_MODULE', $route['module']);
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
     * Instancie et retourne la surcharge de Module du module $module['name']
     * Retourne NULL s'il est introuvable dans le dossier modules
     */
    private function instanciateModule($module)
    {
        $moduleDir = MODS_PATH . strtolower($module['name']) . SEP;
        if(!is_dir($moduleDir))
            return NULL;
        $module['settings'] = empty($module['settings']) 
                                ? array() : unserialize($module['settings']);

        $className = ucfirst($module['name'] . Module::SUFFIX);
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