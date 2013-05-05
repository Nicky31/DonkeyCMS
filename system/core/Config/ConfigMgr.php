<?php

/*
 * Gestion des configurations
 */

class ConfigMgr implements Singleton
{
    // Instance du Config Manager
    private static $_instance = NULL;
    // Instance du loader
    private        $_loader   = NULL;
    // Tableau des configs instanciées
    private        $_configs  = array();
    
    public static function &instance($args = NULL)
    {
        if(!self::$_instance)
            if($args != NULL)
                self::$_instance = new self($args);
            else
                self::$_instance = new self;
        
        return self::$_instance;
    }
    
    private function __construct()
    {
        $this->_loader =& Loader::instance();
    }
    
    /*
     * Note : Les configs.php doivent retourner l'array
     */
    public function loadConfig($path,$name)
    {
        if(isset($this->_configs[$name]))
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : La configuration <b>'. $name .'</b> est déjà chargée !');
            return FALSE;
        }
        
        $params = array(
            'path' => $path,
            'name' => $name
        );
        return $this->_configs[$name] =& $this->_loader->instanciate('system/core/Config/Config',$params);
    }
    
    public function getConfig($name)
    {
        if(isset($this->_configs[$name]))
            return $this->_configs[$name];
        
        throw new Exception('<b>'. __CLASS__ .'</b> : Configuration <b>'. $name .'</b> inexistante ou non chargée.');
    }   
}