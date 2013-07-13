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
        list(, $callContext) = debug_backtrace();
        $curModule = $callContext['object']->module();
        $templateDetails = array(
            'module' => $curModule->name(),
            'theme'  => self::themeOfModule($curModule->name())
        );
        
        if(!file_exists($viewPath = MODS_PATH . $templateDetails['module'] . '/themes/' . $templateDetails['theme'] . '/views/' . $view))
        {
            throw new Exception('<b>' . __CLASS__ .'</b> : Module <b>' . $templateDetails['module'] .'</b> : Thème <b>' . $templateDetails['theme'] .'</b> ou vue <b>'. $view .'</b> inexistante.');
            return;
        }
                
        $this->_views[$var] = self::generateView($viewPath, array_merge($params, $this->_params), $templateDetails);      
    }
    
    public function render()
    {  
        $template = array(
            'module' => MAIN_MODULE,
            'theme' => self::themeOfModule(MAIN_MODULE)
        );
        
        if(!file_exists($templatePath = MODS_PATH . $template['module'] . '/themes/' . $template['theme'] . '/template.php'))
        {
            throw new Exception('<b>' . __CLASS__ .'</b> : Module <b>' . $template['module'] .'</b> : Thème <b>' . $template['theme'] .'</b> ou <b>template.php</b> inexistant');
            return;
        }
        
        echo self::generateView($templatePath, array_merge($this->_params, $this->_views), $template);
    }
    
    /*
     * Méthode principalement afin d'isoler l'extraction des paramètres pour ne pas écraser des variables
     * des méthodes view/render
     */
    private static function generateView($viewPath, $params, $templateDetails)
    {
        extract($params);
        // Nécessaire aux fonctions d'urls (assets helper) pour déterminer les chemins
        $GLOBALS['curTemplate'] = $templateDetails;
        ob_start();
        include $viewPath;
        $content = ob_get_contents();
        ob_end_clean();
        unset($GLOBALS['curTemplate']);
        return $content;
    }
}