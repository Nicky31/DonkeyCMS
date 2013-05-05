<?php

/*
     * Attrape les exceptions & les affiche en stoppant l'exécution du système
     * $exception > Objet exception
     * Merci @SgtFatality, auteur de la fonction
     */
    function exception_handler($exception)
    {
        if(!DEBUG_MODE)
            exit('<script>alert(\'Une erreur est survenue, veuillez excuser la potentielle gêne occasionnée.\');</script>');
        
        $trace = '';
        foreach($exception->getTrace() as $k => $v)
        {
            if(!empty($v['line']) AND !empty($v['file']))
            {
                $function = !empty($v['function']) ? ' <strong> dans la fonction </strong>'. $v['function'] .'('. @implode(', ',$v['args']) .')' : '';
                $class = !empty($v['class']) ? ' <strong> de la classe</strong> '. $v['class'] : '';
                $trace .= '<strong>Ligne : </strong>' . $v['line'] . ' <strong>du fichier : </strong> ' . $v['file'] . $function . $class .'<br />';
            }
        }
        
        exit('<html lang="fr"><head><meta charset="utf-8" /> <title>Exception survenue</title > </head>
              <div style="background-color: #F6DDDD; border: 1px solid #FD1717; color: #8C2E0B; padding: 10px;">
              <h4>Une exception est survenue</h4>
              <strong>Message : </strong>' . $exception->getMessage() . '<br /><br />
              <strong>Ligne : </strong>' . $exception->getLine() . ' <strong>du fichier : </strong> ' . $exception->getFile() . '<br /><br />
              <strong>Appel : </strong><br /><pre>'
              . $trace .
              '</pre></div>
               </html>');
    }
    
/*
 * Classe moteur du CMS
 */

class Donkey
{
    // Instance du loader
    private $_loader    = NULL;
    // Classe config du système
    private $_sysConfig = NULL;
    // Tableau des modules chargés
    private $_modules   = array();
    
    public function __construct(&$Loader)
    {
        $this->_loader =& $Loader;

        $ConfigMgr =& $Loader->instanciate('system/core/Config/ConfigMgr');
        $this->_sysConfig = $ConfigMgr->loadConfig('application/config/sys_config', 'sysConfig');
        set_exception_handler('exception_handler');
    }
    
    public function run()
    {
        $route = $this->route();
        $this->runModule($route['module'], $route['controller'], $route['action']);
    }
    
    public function runModule($module,$controller,$action)
    {
        $moduleDir = BASE_PATH . SEP . APP_DIR . '/modules/' . $module . SEP;
        if(!is_dir($moduleDir))
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : Module <b>'. $module .'</b> inexistant !');
            return;
        }
        
        $this->_loader->getFile('system/core/module/Module');
        $params = array(
            'name' => $module,
            'controller' => $controller
        );
        // Une surcharge de la classe module existe
        if(file_exists($moduleDir . ucfirst($module) . EXT))        
            $module =& $this->_loader->instanciate('application/modules/' . $module . '/' . ucfirst($module), $params);
        else
            $module =& $this->_loader->instanciate('Module', $params);
            
        $this->_modules[] =& $module;   
        $module->run($action);
    }


    public function route()
    {
        $module = $this->_sysConfig['defaultModule'];
        $controller = $this->_sysConfig['defaultController'];
        $action = $this->_sysConfig['defaultAction'];
        
        if(!empty($_GET[$this->_sysConfig['routeGet']]))
        {
            $route = explode('/',$_GET[$this->_sysConfig['routeGet']]);
            
            $module = $route[0];
            
            if(sizeof($route) > 1)
                $controller = $route[1];
           
            if(sizeof($route) > 2)
                $action = $route[2];
        }

        return array(
            'module' => $module,
            'controller' => $controller,
            'action' => $action
        );
    }
    
}