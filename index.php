<?php

/*
 * Bienvenue sur DonkeyCMS
 * CMS gratuit et open source se voulant modulable
 * Développement démarré le soir 2/05/13
 * Auteur : Nicky31
 */

/*
 * -----------------------------
 * Début configuration constantes
 */
// Partie à configurer : 
/*
 * Nom du site, apparaît notamment dans le titre de l'onglet
 */
define('NAME',       'DonkeyCMS');
/*
 * Url du site menant à la racine du CMS, sans / final
 */
define('BASE_URL',   'http://localhost/DonkeyCMS');
/*
 * Mode débug :
 * Mettre true en période de développement pour activer les assertions et erreurs PHP
 * Mettre false en période de production pour gagner des performances et cacher des informations sensibles visibles sur les erreurs
 */
define('DEBUG_MODE', TRUE);

// Configuration secondaire reservée aux développeurs :
/*
 * Extension des fichiers php
 * .php par défaut
 */
define('EXT',        '.php');
/*
 * Séparateur
 * '/' par défaut
 */
define('SEP',        '/');
/*
 * Arborescence : nom des dossiers principaux
 */
define('SYS_DIR',    'system');
define('APP_DIR',    'application');
define('ASSETS_DIR', 'themes');
// Chemin jusqu'à la racine du CMS
define('BASE_PATH',  __DIR__);
/*
 * Fin configuration constantes
 * ----------------------------
 * Début système
 */

if(DEBUG_MODE)
{
    error_reporting(E_ALL | E_STRICT);
    assert_options(ASSERT_ACTIVE, 1);
}
else
{
    error_reporting(0);
    assert_options(ASSERT_ACTIVE, 0);
}
    
session_start(); 

require_once SYS_DIR . SEP . 'core/Loader' . EXT;

$Loader =& Loader::getInstance();

$Donkey1 =& $Loader->instanciate('system/core/Donkey' . EXT,'Instance 1 faite ');