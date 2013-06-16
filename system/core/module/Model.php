<?php

/*
 * Classe mère de tous les models
 */

abstract class Model
{
    // Objet (PDO) représentant la connexion
    protected $_db = NULL;
    
    public function __construct($dbName)
    {
        $pathConfigDb = 'inc/config/databases_config';
        $connectionsMgr =& Loader::instance()->instanciate('system/core/databases/ConnectionsMgr', $pathConfigDb);
        $this->_db =& $connectionsMgr->getConnection($dbName);
    }
    
    public function switchDatabase($dbName)
    {
        $this->_db->query('USE '. $dbName);
    }
}