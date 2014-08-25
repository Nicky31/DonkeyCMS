<?php

/*
 * Classe contenant un module respectant le pattern MVC
 * Chaque module doit avoir une classe héritant de celle-ci à la racine de leur dossier
 */

abstract class Module
{
    // Suffixe utilisé par tous les modules
    const     SUFFIX             = 'Module';  
    // Thème utilisé par défaut
    const     DEFAULT_THEME      = 'default';
    // Controller instancié par défaut si non renseigné par l'url ou invalide
    const     DEFAULT_CONTROLLER = 'home';

    // Nom du module
    protected $_moduleName       = NULL;
    // Classe controller chargée
    protected $_controller       = NULL;
    // Tableau des différents modèles chargés
    protected $_models           = array();
    // Configuration principale du module
    protected $_moduleConfig     = NULL;
    // Views chargées par le module et extraites lors de l'affichage du layout
    protected $_views            = array();

    public function __construct($module)
    {
        $this->_moduleName = $module['name'];
        $this->_moduleConfig = ConfigMgr::instance()->loadConfig($module['settings'], ucfirst($module['name']) . 'ModuleConfig');
        
        OutputContent::assignGlobal('config', $this->config()->content());
    }
    
    public function run($controller, $action)
    {
        $controller = $controller == '' ? static::DEFAULT_CONTROLLER : $controller;

        if($this->_controller == NULL)
        {
            if(!file_exists(MODS_PATH . $this->_moduleName . '/controllers/' . ucfirst($controller . Controller::SUFFIX) . EXT))
            {
                throw new DkException('controller.inexistant', ucfirst($controller . Controller::SUFFIX), $this->_moduleName);
            } 

            $controllerName = ucfirst($controller . Controller::SUFFIX);
            $this->_controller = new $controllerName($this);
        }

        if(method_exists($this->_controller, $action))
            call_user_func(array($this->_controller,$action));
        else
            call_user_func(array($this->_controller, $controllerName::DEFAULT_ACTION));
    }
    
    public function name()
    {
        return $this->_moduleName;
    }

    public function config()
    {
        return $this->_moduleConfig;
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

    public function getTheme()
    {
        $curTheme = static::DEFAULT_THEME;
        if(isset($_COOKIE[DATASDONKEY]['modules'][$this->_moduleName]['defaultTheme']))
        {
            $dirTheme = realpath(MODS_PATH . SEP . $this->_moduleName . '/themes/' . $_COOKIE[DATASDONKEY]['modules'][$this->_moduleName]['defaultTheme']);
            if(is_dir($dirTheme) && basename(dirname($dirTheme)) == 'themes')   
            {
                $curTheme = $_COOKIE[DATASDONKEY]['modules'][$this->_moduleName]['defaultTheme'];
            }
        }
        
        return $curTheme;
    }

    /*
     * Ajoute des paramètres de connexions pour une nouvelle base de donnée à la dbsConfig
     */
    public function registerDatabase($dbName, $params)
    {
        ConfigMgr::instance()->getConfig('dbsConfig')->rewriteItem($dbName, $params);
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
    public function partialRender(OutputContent $mainOutput)
    {

    }    
 }