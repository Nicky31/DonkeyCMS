<?php

class ConnectionsMgr extends Singleton
{
    // Configuration des dbs
    private $_dbsConfigs  = NULL;
    // Tableau des objets PDO représentant les connexions
    private $_connections = array();

    protected function __construct()
    {
        $this->_dbsConfigs = ConfigMgr::instance()->getConfig('dbsConfig');
    }
    
    public function getConnection($name)
    {
        if(isset($this->_connections[$name]))
            return $this->_connections[$name];
        else // Connexion jusque là inexistante, on tente de l'établir
            return $this->initializeConnection ($name);
    }
    
    private function initializeConnection($name)
    {
        if(!($config = $this->_dbsConfigs[$name]))
        {
            throw new DkException('database.inexistant_configuration', $name);
        }
        
        $db = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['name'] , $config['user'] , $config['password'], $config['options']);
        $this->_connections[$name] = $db;
        
        return $db;
    }
    
}