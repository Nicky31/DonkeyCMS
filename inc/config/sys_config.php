<?php
/*
 * Configuration du système
 * Ne pas modifier sans savoir ce que vous faites 
 */

return array(
    // Module par défaut si aucune route n'est indiquée
    'defaultModule' => 'home',
    // Controller par défaut si non indiqué dans la route. Utilisé pour tous les modules
    'defaultController' => 'home',
    // Méthode par défaut si non indiquée dans la route. Utilisé pour tous les modules
    'defaultAction' => 'index',
    // Nom du $_GET indiquant la route
    'routeGet' => 'p',
    // Suffixe des des fichiers+Classes Modules
    'moduleSuffix' => 'Module',
    // Suffixe des fichiers+Classes Controllers
    'controllerSuffix' => 'Controller',
    // Suffixe des fichiers+Classes Models
    'modelSuffix' => 'Model',
    // Thème par défaut de tous les modules. Modifiable dans la config des modules sur le même index
    'defaultTheme' => 'default',
    // Nom du thème partagé par les autres thèmes de chaque module. Modifiable dans la config des modules sur le même index
    'sharedTheme' => 'shared',
    // Nom des sessions & cookies relatifs aux données internes de DonkeyCMS
    'datasDonkey' => 'DonkeyDatas'
);