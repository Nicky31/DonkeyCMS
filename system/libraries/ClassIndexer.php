<?php
    /*
     * Indexe toutes les classes du projet afin de pouvoir les inclure dynamiquement (Autoloader)
     */

abstract class ClassIndexer
{
    // Extensions des fichiers cibles
    private static $_ext           = '.php';
    // Dossiers à ne pas parcourir
    private static $_dirsForbidden = array();
    // Mise en cache des résultats de processDir activée ?
    private static $_cacheEnabled  = FALSE;
    // Dossier du cache
    private static $_cacheDir      = NULL;

    public static function init($enableCache, $cacheDir, $dirsForbidden = array())
    {
        self::setCache($enableCache, $cacheDir);
        self::addDirsForbidden($dirsForbidden);
    }

    public static function setExt($ext)
    {
        self::$_ext = $ext;
    }

    public static function addDirsForbidden($dirsForbidden)
    {
        self::$_dirsForbidden = array_merge(self::$_dirsForbidden, $dirsForbidden);
    }

    public static function setCache($enabled, $cacheDir)
    {   
        self::$_cacheEnabled = $enabled;
        self::$_cacheDir = $cacheDir;
    }

    /*
     * Recherche toutes les classes contenues dans $dirPath & ses sous répertoires
     * Renvoit array['NomClasse'] = 'CheminFichier'
     */
    public static function processDir($dirPath)
    {
        $cachePath = rtrim(self::$_cacheDir) . DIRECTORY_SEPARATOR . self::cacheName($dirPath);
        // Récupération du cache si activé
        if(self::$_cacheEnabled)
        {
            if(file_exists($cachePath))
            {
                return include $cachePath;
            }
        }

        $classes = array();
        foreach(self::parseDir($dirPath) as $file)
        {
            if(($newClasses = self::findClass($file)) !== FALSE)
            {
                $classes = array_merge($classes, $newClasses);
            }
        }

        file_put_contents($cachePath, '<?php '. "\n\nreturn " . var_export($classes, true) . ';');

        return $classes;
    }

    // Retourne le nom du fichier de cache devant contenir le listing des classes de $targetPath
    private static function cacheName($targetPath)
    {
        return 'listing_'. basename($targetPath) . self::$_ext;
    }

    /*
     *  Parcoure tous les fichiers du répertoire $baseDir et de ses sous répertoires. 
     *  Ne renvoit que ceux portant l'extension self::$_ext
     */ 
    public static function parseDir($dirPath)
    {
        // Tableau de tous les fichiers trouvés
        static $files;

        $subElements = glob($dirPath . DIRECTORY_SEPARATOR . '*');

        foreach($subElements as $subElement)
        {
            if(strpos($subElement, '.') !== FALSE) // C'est un fichier
            {
                if(strpos($subElement, self::$_ext) !== FALSE) // Bien un fichier PHP
                {
                    $files[] = $subElement;
                }
            }
            else // Dossier : on le parcoure
            {
                if(!in_array(basename($subElement), self::$_dirsForbidden))
                {
                    self::parseDir($subElement);
                }
            }
        }

        return $files;
    }

    /*
     *  Cherche une ou plusieurs classes & interfaces dans le fichier dont le chemin est indiqué dans $filePath.
     *  Renvoit les classes/interfaces du fichier sous forme d'array numéroté ou FALSE si aucune n'est trouvée
     */
    public static function findClass($filePath)
    {
        $newClasses = array();
        $fileTokens = token_get_all(file_get_contents($filePath));
        $nbTokens = count($fileTokens);

        for($i = 0; $i < $nbTokens; ++$i)
        {
            // Mot-clé "class" ou "interface"
            if($fileTokens[$i][0] == T_CLASS || $fileTokens[$i][0] == T_INTERFACE)
            {
                for($i; $fileTokens[$i][0] != T_STRING; ++$i);
                $newClasses[$fileTokens[$i][1]] = $filePath;
            }
        }

        if(count($newClasses))
            return $newClasses;
        else
            return FALSE;
    }
}