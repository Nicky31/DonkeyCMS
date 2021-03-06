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
   
        /*
         * Aucun chemin spécifié : on le détermine à partir du module à l'origine de cet appel
         * Utilisation déconseillée étant donnée cette de debug_backtrace() : performances douteuses
         */
        if($dir == '')
        {
            list(, $callContext) = debug_backtrace();
            $dir = MODS_DIR . SEP . $callContext['object']->module()->moduleName() . '/langs';
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
            throw new DkException('lang.inexistant_file', $name, $dir);
        }
    }
    
    public static function tr($name, $args = array())
    {
        // Contenus contenant des espaces = ne doivent pas être traduits
        // (exceptions classe Lang disfonctionnant, tentative de traduction pourrait entrainer boucle infinie)
        if(strrpos($name, ' ') !== FALSE)
        {
            return $name;
        }
        
        $cookieLang = self::cookieLang();
        // Langue stockée en cookie & donc choisie par l'utilisateur ?
        if(isset(self::$_translations[$cookieLang][$name]))
        {
            return vsprintf(self::$_translations[$cookieLang][$name], $args);
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
            throw new DkException('lang.inexistant_tr', $name);
        }
    }
    
    public static function cookieLang()
    {
        return (isset($_COOKIE[DATASDONKEY]['defaultLang']) && strlen($_COOKIE[DATASDONKEY]['defaultLang']) <= 3) ? 
            $_COOKIE[DATASDONKEY]['defaultLang'] : '';
    }
    
    public static function userLang()
    {
        return $_SERVER['HTTP_ACCEPT_LANGUAGE'][0].$_SERVER['HTTP_ACCEPT_LANGUAGE'][1];
    }
}