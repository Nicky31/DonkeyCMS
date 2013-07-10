<?php

/*
 * Classe de gestion des traductions / langues
 */

abstract class Lang
{
    /*
     * Tableau de tableaux des différentes traductions.
     * Index correspondant à chaque sous tableau  = langue correspondante au sous tableau
     * Index des sous tableaux = Nom du message/texte
     */
    private static $_translations = array();
    
    public static function loadTranslations($name, $dir = '')
    {
        $name = appendExt($name);
   
        // Aucun chemin spécifié : on le détermine à partir du module à l'origine de cet appel
        if($dir == '')
        {
            $callContext = debug_backtrace();
            $callContext = $callContext[1];
            $dir = MODS_DIR . SEP . $callContext['object']->module()->name() . '/langs';
        }
        else
        {
            $dir = parsePath($dir);
        }
        
        $availableLangages = glob(BASE_PATH . SEP . $dir . '/*');
        $found = FALSE;
       
        foreach($availableLangages as $curLangage)
        {
            $curLangage = basename($curLangage, SEP);
            if(file_exists($langPath = BASE_PATH . SEP . $dir . SEP . $curLangage . SEP . $name))
            {
                $newTranslations = include $langPath;
                if(empty(self::$_translations[$curLangage]))
                {
                    self::$_translations[$curLangage] = $newTranslations;
                }
                else
                {
                    self::$_translations[$curLangage] = array_merge(self::$_translations[$curLangage], $newTranslations);
                }                
                $found = TRUE;
            }
        }
        
        if(!$found)
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : Fichier langue <b>'. $name . '</b> non trouvé dans <b>'. $dir .'</b>');
        }
    }
    
    public static function tr($name, $args = array())
    {
        $l = self::curLang();
        // Langue stockée en cookie & donc choisie par l'utilisateur ?
        if(isset(self::$_translations[$l][$name]))
        {
            return vsprintf(self::$_translations[$l][$name], $args);
        }
        // Langue de l'utilisateur détectée automatiquement ? 
        else if(isset(self::$_translations[self::userLang()][$name]))
        {
            return vsprintf(self::$_translations[self::userLang()][$name], $args);
        }
        // Langue par défaut du site
        else if(isset(self::$_translations[DEFAULT_LANG][$name]))
        {
            return vsprintf(self::$_translations[DEFAULT_LANG][$name], $args);
        }
        else
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : Demande de la traduction <b>'. $name .'</b> échouée car inexistante ou non chargée.');
        }
    }
    
    public static function curLang()
    {
        return isset($_COOKIE[DATASDONKEY]['defaultLang']) ? $_COOKIE[DATASDONKEY]['defaultLang'] : '';
    }
    
    public static function userLang()
    {
        return $_SERVER['HTTP_ACCEPT_LANGUAGE'][0].$_SERVER['HTTP_ACCEPT_LANGUAGE'][1];
    }
}