<?php

/*
 * Classe contenant un module respectant le pattern MVC
 */

class Module extends ModuleComponent
{
    // Classe controller chargée
    protected $_controller = NULL;
    // Tableau des différents modèles chargés
    protected $_models     = array();
    // Nom de la configuration principale du module
    protected $_configName = 'config.php';
    // Views chargées par le module et extraites lors de l'affichage du layout
    protected $_views      = array();

    public function __construct($params)
    {
        Donkey::instance()->addModule($this);

        $moduleDir = MODS_PATH . $params['name'] . SEP;
        if(!file_exists($moduleDir . 'controllers/' . ucfirst($params['controller'] . CONTROLLERSUFFIX) . EXT))
        {
            throw new DkException('controller.inexistant', ucfirst($params['controller'] . CONTROLLERSUFFIX), $params['name']);
        } 
        
        if(file_exists($moduleDir . 'config/' . $this->_configName))
        {
            $this->_moduleConfig = ConfigMgr::instance()->loadConfig('modules/' . $params['name'] . '/config/myConfig.php', ucfirst($params['name']) . 'ModuleConfig');   
        }
        else
        {
            $this->_moduleConfig = ConfigMgr::instance()->loadConfig($this->initConfig(), ucfirst($this->_name) . 'ModuleConfig');   
        }
        parent::__construct($params['name']);
        
        $this->_loader = Loader::instance();
        $controllerName = ucfirst($params['controller'] . CONTROLLERSUFFIX);
        $this->_controller = new $controllerName($params['name']);
        //$this->_controller->setModuleName($this->_moduleName);
        OutputContent::assignGlobal('config', $this->config()->content());
    }
    
    public function run($action)
    {
        if(method_exists($this->_controller, $action))
            call_user_func(array($this->_controller,$action));
        else
            throw new DkException('controller.action_inexistant', $action, get_class($this->_controller), $this->_name);
    }
    
    protected function initConfig()
    {
        /*
         * Si une configuration personelle du module est faite,
         * celle ci doit retourner un array contenant toutes les clés ci-dessous.
         */
        return array(
          'defaultTheme' => DEFAULTTHEME
        );
    }
    
    public function addModel($model)
    {
        $this->_models[get_class($model)] = $model;
    }

    public function addView($view, $key = 'content')
    {
        $this->_views[$key] = $view;
    }

    public function getViews()
    {
        return $this->_views;
    }

    /*
     * Retourne le layout du module afin de remplir la principale variable de template
     * Méthode uniquement appellée si c'est le module maître (= le premier appellé)
     */
    public function render()
    {
        return new OutputContent(Finder::layoutPath('template.php', $this->_moduleName, $this->getTheme()), $this->_views);  
    }

    /*
     * Effectue différents traitements finaux afin d'intégrer les views chargées dans d'autres views/templates
     * Méthode uniquement appellée si c'est un module secondaire (= tout autre que le premier appellé)
     * A implémenter par les modules eux-ême
     */
    public function partialRender()
    {

    }    
 }