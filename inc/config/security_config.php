<?php

/*
 * Configuration des règles de sécurisation de données entrantes
 */

return array(
    'GET' => array(
        'pattern'   => '#^[a-z0-9/_-]{1,32}$#i',
        // Règles spécifiées pour des index donnés
        'dedicated' => array(
            
        )
    ),
    'POST' => array(
        'pattern' => '#^.+$#i'
    ),
    'COOKIE' => array(
        'pattern' => '#^[a-z0-9/_-]{1,32}$#i'
    )
    
);