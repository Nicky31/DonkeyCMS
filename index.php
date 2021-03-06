<?php
$time_start = microtime(true);
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
define('NAME',         'DonkeyCMS');
/*
 * Url du site menant à la racine du CMS, sans / final
 */
define('BASE_URL',     'http://localhost/DonkeyCMS');
/*
 * Mode débug :
 * Mettre true en période de développement pour activer les assertions et erreurs PHP
 * Mettre false en période de production pour gagner des performances et cacher des informations sensibles visibles sur les erreurs
 */
define('DEBUG_MODE',   TRUE);
/*
 * Langue par défaut :
 * Valeur = nom des sous dossiers correspondants à la langue dans chaque module
 */
define('DEFAULT_LANG', 'fr');

// Configuration secondaire reservée aux développeurs :
/*
 * Extension des fichiers php
 * .php par défaut
 */
define('EXT',          '.php');
/*
 * Séparateur
 * '/' par défaut
 */
define('SEP',          DIRECTORY_SEPARATOR);
/*
 * Arborescence : nom des dossiers principaux
 */
define('SYS_DIR',      'system');
define('INC_DIR',      'inc');
define('CACHE_DIR',    'cache');
define('LOG_DIR',      'log');
define('MODS_DIR',     'modules');
/*
 * Arborescence : chemins principaux
 */
define('BASE_PATH',    __DIR__);
define('SYS_PATH',     BASE_PATH . SEP . SYS_DIR   . SEP);
define('INC_PATH',     BASE_PATH . SEP . INC_DIR   . SEP);
define('CACHE_PATH',   BASE_PATH . SEP . CACHE_DIR . SEP);
define('LOG_PATH',     BASE_PATH . SEP . LOG_DIR   . SEP);
define('MODS_PATH',    BASE_PATH . SEP . MODS_DIR  . SEP);
/*
 * Version du CMS
 */
define('vDONKEY', 1);

/*
* Fin configuration constantes
* ----------------------------
* Début système
*/

if(DEBUG_MODE)
{
    error_reporting(E_ALL | E_STRICT);
    assert_options(ASSERT_ACTIVE, 1);
    assert_options(ASSERT_BAIL, 1);
}
else
{
    error_reporting(0);
    assert_options(ASSERT_ACTIVE, 0);
}

require SYS_PATH . 'core/Singleton'                      . EXT;
require SYS_PATH . 'core/Loader'                         . EXT;
require SYS_PATH . 'libraries/ClassIndexer/ClassIndexer' . EXT;

ClassIndexer::init(!DEBUG_MODE, CACHE_PATH .'ClassIndexer', 
					array( LOG_DIR, 'langs', 'themes', 'nbproject', 'simpletest' ));
Loader::instance(ClassIndexer::processDir(BASE_PATH));

$Donkey = Donkey::instance();
$Donkey->run();

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Temps d'exécution : $time secondes\n";