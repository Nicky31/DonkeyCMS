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
        
        $this->_configs[$name] = new Config($this->_loader->getFile($path, TRUE), $name);
        return $this->_configs[$name];
    }
    
    public function getConfig($name)
    {
        if(isset($this->_configs[$name]))
            return $this->_configs[$name];
        
        throw new Exception('<b>'. __CLASS__ .'</b> : Configuration <b>'. $name .'</b> inexistante ou non chargée.');
    }
    
    
}

class Config implements ArrayAccess
{
    private $_content = array();
    private $_name    = NULL;
    
    public function __construct($arrayConfig,$configName)
    {
        $this->_content = $arrayConfig;
        $this->_name = $configName;
    }
    
    public function getItem($k)
    {
        if($this->itemExists($k))
            return $this->_content[$k];
        
        throw new Exception('<b>'. __CLASS__ . '</b> : L\'item <b>'. $k .'</b> n\'existe pas dans la configuration <b>'. $this->_name .'</b> !');
    }
    
    public function rewriteItem($k,$v)
    {
        $this->_content[$k] = $v;
    }
    
    public function itemExists($k)
    {
        return isset($this->_content[$k]);
    }
    
    public function unsetItem($k)
    {
        if($this->itemExists($k))
        {
            unset($this->_content[k]);
            return TRUE;
        }
        return FALSE;
    }
    
    public function offsetSet($offset, $value)
    {
        return $this->rewriteItem($offset, $value);
    }
    
    public function offsetGet($offset)
    {
        return $this->getItem($offset);
    }
    
    public function offsetExists($offset)
    {
        return $this->itemExists($offset);
    }
    
    public function offsetUnset($offset)
    {
        return $this->unsetItem($offset);
    }
}