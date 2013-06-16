<?php
/*
 * Classe mère de tous les controllers
 */

abstract class Controller
{
    // Instance du module propriétaire du controller courant
    protected $_module = NULL;
    // Instance du loader
    protected $_loader = NULL;
    
    public function __construct(&$Module)
    {
        $this->_module =& $Module;
        $this->_loader =& Loader::instance();
    }
    
    public function helper($name)
    {
        if(is_array($name))
        {
            foreach ($name as $curName)
                $this->helper ($curName);
            return;
        }
        
        if(!$this->_loader->getFile('modules/' . $this->_module->name() . '/helpers/'. $name , FALSE, FALSE))
            if(!$this->_loader->getFile('inc/helpers/'. $name , FALSE, FALSE))
                if(!$this->_loader->getFile('system/helpers/' . $name, FALSE, FALSE))
                    throw new Exception('<b>' . __CLASS__ . '</b> : Demande d\'inclusion du helper <b>' . $name .'</b> échouée car fichier introuvable. <br /> Les helpers doivent être placés dans un sous dossier <b>inc/helpers</b> <u>OU</u> dans un sous dossier <b>helpers</b> du module <u>OU</u> dans le sous dossier <b>system/helpers</b>');
    }
    
    public function &library($name, $params = array())
    {
        $library = NULL;
        
        if(!$library =& $this->_loader->instanciate('modules/' . $this->_module->name() . '/libraries/'. $name, $params, FALSE))
            if(!$library =& $this->_loader->instanciate('inc/libraries/'. $name, $params, FALSE))
                if(!$library =& $this->_loader->instanciate('system/libraries/'. $name, $params, FALSE))
                {
                    throw new Exception('<b>' . __CLASS__ . '</b> : Demande d\'inclusion de la librairie <b>' . $name .'</b> échouée car fichier introuvable. <br /> Les librairies doivent être placées dans un sous dossier <b>inc/libraries</b> <u>OU</u> dans un sous dossier <b>libraries</b> du module <u>OU</u> dans le sous dossier <b>system/libraries</b>');
                    return;
                }
            
        return $library;
    }
    
    public function &model($modelName,$dbName = '')
    {
        $this->_loader->getFile('system/core/module/Model');
        $model = NULL;
        
        if($model =& $this->_loader->instanciate('modules/' . $this->_module->name() . '/models/'. $modelName . MODELSUFFIX,$dbName))
        {
            $this->_module->addModel($model);
            return $model;
        }
        
        return FALSE;
    }
    
    public function output()
    {
        return Output::instance();
    }
    
    public function module()
    {
        return $this->_module;
    }
    
    public function selectTheme()
    {
        call_user_func_array(array($this,DEFAULTACTION), array());
        
        if(empty($_GET['theme']))
            return;
        if(!is_dir(MODS_PATH . $this->module()->name() .'/themes/' . $_GET['theme']))
            return;
        
        setcookie(DATASDONKEY .'[modules]['.$this->module()->name().'][defaultTheme]', $_GET['theme'], time() + 365*24*3600, null, null, false, true);
        echo redirect();
    }
    
    public function selectLang()
    {
        call_user_func_array(array($this,DEFAULTACTION), array());
        
        if(empty($_GET['lang']))
            return;
        
        setcookie(DATASDONKEY .'[defaultLang]', $_GET['lang'], time() + 365*24*3600, null, null, false, true);
        echo redirect(); 
    }
}