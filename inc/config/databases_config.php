<?php
/*
 * Configurations des connexions SQLS
 */
 
return array(
    // Nom de la connexion utilisé en interne
    'dbStatic' => array(
        // Nom de la db sélectionnée par défaut
        'name'     => 'ancestra_static',
        'host'     => 'localhost',
        'port'     => 3306,
        'user'     => 'root',
        'password' => '',
        'options'  => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING
        )
    )
);