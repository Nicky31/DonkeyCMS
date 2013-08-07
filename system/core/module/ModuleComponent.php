<?php

/*
 * Parent de tous les classes composantes d'un module (Module/Model/Controller)
 * Implémente les attributs et méthodes devant être accessibles dans l'ensemble du module
 */

abstract class ModuleComponent
{
    // Nom du module
    protected $_moduleName;
    // Objet module
    protected $_module       = NULL;
    // Config du module
    protected $_moduleConfig = NULL;

    public function __construct($moduleName)
    {
        $this->setModuleName($moduleName);
    }

    public function setModuleName($moduleName)
    {
        $this->_moduleName = $moduleName;
        $this->initComponent();
    }

    public function initComponent()
    {
        $this->_moduleConfig = Donkey::instance()->module($this->_moduleName)->config();
        $this->_module = Donkey::instance()->module($this->_moduleName);
    }

    public function module()
    {
        return $this->_module;
    }

    public function name()
    {
        return $this->_moduleName;
    }

    public function config()
    {
        return $this->_moduleConfig;
    }

    public function getConfig()
    {
        return $this->_moduleConfig;
    }

    public function selectTheme()
    {
        call_user_func_array(array($this, DEFAULTACTION), array());
        echo redirect();

        if(($theme = Input::get(0, 'GET')) === NULL)
        {
            return;
        }
        if(!is_dir(MODS_PATH . $this->_moduleName .'/themes/' . $theme))
        {
            return;
        }
        
        setcookie(DATASDONKEY .'[modules]['.$this->_moduleName.'][defaultTheme]', $theme, time() + 365*24*3600, '/', null, false, true);
    }
    
    public function selectLang()
    {
        call_user_func_array(array($this, DEFAULTACTION), array());
        echo redirect(); 

        if(($lang = Input::get(0, 'GET')) === NULL ||
            $lang != NULL && strlen($lang) > 2)
            return;
        
        setcookie(DATASDONKEY .'[defaultLang]', $lang, time() + 365*24*3600, '/', null, false, true);
    }

    public function getTheme()
    {
        $curTheme = '';
        
        $curTheme = $this->_moduleConfig->item('defaultTheme');
        if(isset($_COOKIE[DATASDONKEY]['modules'][$this->_moduleName]['defaultTheme']))
        {
            $curTheme = $_COOKIE[DATASDONKEY]['modules'][$this->_moduleName]['defaultTheme'];
        }
        
        
        return $curTheme;
    }
}