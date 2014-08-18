<?php
/*
 * Configurations des connexions SQLS
 */
 
return array(
    // Nom de la connexion utilisé en interne
    'donkeyDb' => array(
        // Nom de la db sélectionnée par défaut
        'name'     => 'donkey_db',
        'host'     => 'localhost',
        'port'     => 3306,
        'user'     => 'root',
        'password' => '',
        'options'  => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    )
);