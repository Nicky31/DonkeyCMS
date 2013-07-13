<?php

/*
 * Classe contenant un module respectant le pattern MVC
 */

class Module
{
    // Nom du module
    protected $_name       = NULL;
    // Classe controller chargée
    protected $_controller = NULL;
    // Tableau des différents modèles chargés
    protected $_models     = array();
    // Nom de la configuration principale du module
    protected $_configName = 'config.php';
    // Configuration principale du module
    protected $_config     = NULL;
    
    public function __construct($params)
    {
        $moduleDir = MODS_PATH . $params['name'] . SEP;
        if(!file_exists($moduleDir . 'controllers/' . ucfirst($params['controller'] . CONTROLLERSUFFIX) . EXT))
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : Controller <b>'. ucfirst($params['controller'] . CONTROLLERSUFFIX) .'</b> du module <b>'. $params['name'] .'</b> inexistant !');
            return;
        } 
        
        if(file_exists($moduleDir . 'config/' . $this->_configName))
        {
            $this->_config =& ConfigMgr::instance()->loadConfig('modules/' . $params['name'] . '/config/myConfig.php', ucfirst($params['name']) . 'ModuleConfig');   
        }
        else
        {
            $this->_config =& ConfigMgr::instance()->loadConfig($this->initConfig(), ucfirst($this->_name) . 'ModuleConfig');   
        }
        
        $this->_name = $params['name'];
        $this->_loader =& Loader::instance();
        $this->_controller =& $this->_loader->instanciate('modules/' . $params['name'] . '/controllers/' . ucfirst($params['controller'] . CONTROLLERSUFFIX), $this);
        
        Output::instance()->addParam('config', $this->config(TRUE));
    }
    
    public function run($action)
    {
        if(method_exists($this->_controller, $action))
            call_user_func(array($this->_controller,$action));
        else
            throw new Exception('<b>' . __CLASS__ . '</b> : Action <b> ' . $action . '</b> du controller <b> ' . get_class($this->_controller) .'</b> du module <b> ' . $this->_name . '</b> inexistante ! ');
    }
    
    protected function initConfig()
    {
        /*
         * Si une configuration personelle du module est faite,
         * celle ci doit retourner un array contenant toutes les clés ci-dessous.
         */
        return array(
          'defaultTheme' => DEFAULTTHEME,
          'sharedTheme'  => SHAREDTHEME
        );
    }
    
    public function name()
    {
        return $this->_name;
    }
    
    public function config($content = FALSE)
    {
        if(is_array($this->_config))
            return $this->_config;
        else if($content)
            return $this->_config->content();
        else
            return $this->_config;
    }
    
    public function addModel($model)
    {
        $this->_models[get_class($model)] =& $model;
    }
}