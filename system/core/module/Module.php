<?php

/*
 * Classe contenant un module respectant le pattern MVC
 */

class Module
{
    // Nom du module
    protected $_moduleName   = NULL;
    // Classe controller chargée
    protected $_controller   = NULL;
    // Tableau des différents modèles chargés
    protected $_models       = array();
    // Configuration principale du module
    protected $_moduleConfig = NULL;
    // Views chargées par le module et extraites lors de l'affichage du layout
    protected $_views        = array();

    public function __construct($moduleName)
    {
        $this->_moduleName = $moduleName;

        if(file_exists(MODS_PATH . $moduleName . '/config/config.php')) // Le module a-t-il sa propre config ?
        {
            $this->_moduleConfig = ConfigMgr::instance()->loadConfig('modules/' . $moduleName . '/config/config.php', ucfirst($moduleName) . 'ModuleConfig');   
        }
        else
        {
            $this->_moduleConfig = ConfigMgr::instance()->loadConfig($this->initConfig(), ucfirst($moduleName) . 'ModuleConfig');   
        }
        
        OutputContent::assignGlobal('config', $this->config()->content());
    }
    
    public function run($controller, $action)
    {
        if($this->_controller == NULL)
        {
            if(!file_exists(MODS_PATH . $this->_moduleName . '/controllers/' . ucfirst($controller . CONTROLLERSUFFIX) . EXT))
            {
                throw new DkException('controller.inexistant', ucfirst($controller . CONTROLLERSUFFIX), $this->_moduleName);
            } 

            $controllerName = ucfirst($controller . CONTROLLERSUFFIX);
            $this->_controller = new $controllerName($this);
        }

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
        $curTheme = $this->_moduleConfig->item('defaultTheme');
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