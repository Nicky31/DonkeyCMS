<?php

return array(
    'defaultTheme' => 'default',

    'myDb' => array(
        // Nom de la db sélectionnée par défaut
        'name'     => 'blablah',
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