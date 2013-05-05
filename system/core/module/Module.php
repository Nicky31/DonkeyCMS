<?php

/*
 * Classe gérant un module respectant le pattern MVC
 */

class Module
{
    // Nom du module
    protected $_name       = NULL;
    // Classe controller chargée
    protected $_controller = NULL;
    // Tableau des différents modèles chargés
    protected $_models     = array();
    
    public function __construct($params)
    {
        $moduleDir = BASE_PATH . SEP . APP_DIR . '/modules/' . $params['name'] . SEP;
        if(!file_exists($moduleDir . 'controllers/' . ucfirst($params['controller']) . EXT))
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : Controller <b>'. $params['controller'] .'</b> du module <b>'. $params['name'] .'</b> inexistant !');
            return;
        } 
        
        $this->_name = $params['name'];
        $this->_loader =& Loader::instance();
        $this->_controller =& $this->_loader->instanciate('application/modules/' . $params['name'] . '/controllers/' . ucfirst($params['controller']), $this);
    }
    
    public function run($action)
    {
         if(method_exists($this->_controller, $action))
            call_user_func(array($this->_controller,$action));
        else
            throw new Exception('<b>' . __CLASS__ . '</b> : Action <b> ' . $action . '</b> du controller <b> ' . get_class($this->_controller) .'</b> du module <b> ' . $this->_name . '</b> inexistante ! ');
    }
}