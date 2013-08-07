<?php

/*
 * Classe mère de tous les models
 */

abstract class Model extends ModuleComponent
{
    // Objet (PDO) représentant la connexion
    protected $_db = NULL;
    
    public function __construct($dbName)
    {
        $this->_db = ConnectionsMgr::instance(ConnectionsMgr::CONFIG_PATH) -> getConnection($dbName);
    }
    
    public function switchDatabase($dbName)
    {
        $this->_db->query('USE '. $dbName);
    }
}