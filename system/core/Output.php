<?php

/*
 * Classe Output
 * Traite le flux sortant
 * ---------------------------------------------------------------------------------------
 */

class Output extends Singleton
{
    /*
     * Liste des views chargées
     * Array =>
     * Clés    = nom des variables affichant les contenus respectifs des views dans le template
     * Valeurs = Contenus de la views après traitement (variables remplacés par valeurs) 
     */
    private $_views    = array();
    /*
     * Paramètres additionnels 
     * Extraits avant affichage de chaque view/template
     */
    private $_params   = array();
    
    public function addParam($key,$value)
    {
        $this->_params[$key] = $value;
    }
    
    public static function themeOfModule($module = 0)
    {
        $curTheme = '';
        
        // Valeur par défaut $module  = 0 => Module principal demandé si aucun renseigné
        $curTheme = Donkey::instance()->module($module)->config()->item('defaultTheme');
        $moduleName = Donkey::instance()->module($module)->name();
        if(isset($_COOKIE[DATASDONKEY]['modules'][$moduleName]['defaultTheme']))
            $curTheme = $_COOKIE[DATASDONKEY]['modules'][$moduleName]['defaultTheme'];
        
        return $curTheme;
    }
    
    public function view($view, $params = array(), $var = 'content')
    {
        // On détermine le module & son thème par défaut à l'origine de cet appel
        $callContext = debug_backtrace();
        $callContext = $callContext[1];
        $curModule = $callContext['object']->module();
        $template = array(
            'module' => $curModule->name(),
            'theme'  => self::themeOfModule($curModule->name())
        );
        
        if(!file_exists($viewPath = MODS_PATH . $template['module'] . '/themes/' . $template['theme'] . '/views/' . $view))
        {
            throw new Exception('<b>' . __CLASS__ .'</b> : Module <b>' . $template['module'] .'</b> : Thème <b>' . $template['theme'] .'</b> ou vue <b>'. $view .'</b> inexistante.');
            return;
        }
        
        extract($this->_params);
        extract($params);
        
        // Nécessaire aux fonctions d'urls (assets helper) pour déterminer les chemins
        $GLOBALS['curTemplate'] = $template;
        ob_start();
        include $viewPath;
        $this->_views[$var] = ob_get_contents();
        ob_end_clean();
        unset($GLOBALS['curTemplate']);
    }
    
    public function render()
    {  
        $template = array(
            'module' => Donkey::instance()->module(0)->name(),
            'theme' => self::themeOfModule(Donkey::instance()->module(0)->name())
        );
        
        if(!file_exists($templatePath = MODS_PATH . $template['module'] . '/themes/' . $template['theme'] . '/template.php'))
        {
            throw new Exception('<b>' . __CLASS__ .'</b> : Module <b>' . $template['module'] .'</b> : Thème <b>' . $template['theme'] .'</b> ou <b>template.php</b> inexistant');
            return;
        }
        
        extract($this->_params);
        extract($this->_views);
        
        // Nécessaire aux fonctions d'urls (assets helper) pour déterminer les chemins
        $GLOBALS['curTemplate'] = $template;
        ob_start();
        include $templatePath;
        $page = ob_get_contents();
        ob_end_clean();
        unset($GLOBALS['curTemplate']);
        
        echo $page;
    }
}