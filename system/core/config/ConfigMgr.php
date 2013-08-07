<?php

/*
 * Gestion des configurations
 */

class ConfigMgr extends Singleton
{
    // Instance du loader
    private        $_loader   = NULL;
    // Tableau des configs instanciées
    private        $_configs  = array();

    protected function __construct()
    {
        $this->_loader = Loader::instance();
    }
    
    /*
     * Note : Les configs.php doivent retourner l'array
     */
    public function loadConfig($path,$name)
    {
        if(isset($this->_configs[$name]))
        {
            return $this->_configs[$name];
        }
        
        $params = array(
            'path' => $path,
            'name' => $name
        );
        
        $this->_configs[$name] = new Config($params);
        return $this->_configs[$name];
    }
    
    public function getConfig($name)
    {
        if(isset($this->_configs[$name]))
            return $this->_configs[$name];
        
        throw new DkException('config.load_inexistant', $name);
    }   
}