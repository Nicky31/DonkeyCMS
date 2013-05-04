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
    private $_loader    = NULL;
    private $_route     = array();
    private $_sysConfig = NULL;
    
    public function __construct(&$Loader)
    {
        $Loader->getFile('system/core/Config' . EXT);
        $this->_loader =& $Loader;
        $this->_sysConfig = ConfigMgr::instance()->loadConfig('application/config/sys_config' . EXT, 'sysConfig');
        set_exception_handler('exception_handler');
    }
    
    public function run()
    {
        $this->route();
        
        $moduleDir = BASE_PATH . SEP . APP_DIR . '/modules/' . $this->_route['module'] . SEP;
        if(!is_dir($moduleDir))
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : Module <b>'. $this->_route['module'] .'</b> inexistant !');
            return;
        }
        if(!file_exists($moduleDir . 'controllers/' . ucfirst($this->_route['controller']) . EXT))
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : Controller <b>'. $this->_route['controller'] .'</b> du module <b>'. $this->_route['module'] .'</b> inexistant !');
            return;
        }   
        
        $Controller = $this->_loader->instanciate('application/modules/' . $this->_route['module'] . '/controllers/' . ucfirst($this->_route['controller']) . EXT);
        
        if(method_exists($Controller, $this->_route['action']))
            call_user_func(array($Controller,$this->_route['action']));
        else
            throw new Exception('<b>' . __CLASS__ . '</b> : Action <b> ' . $this->_route['action'] . '</b> du controller <b> ' . $this->_route['controller'] .'</b> du module <b> ' . $this->_route['module'] . '</b> inexistante ! ');
        
        
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
        
        $this->_route['module'] = $module;
        $this->_route['controller'] = $controller;
        $this->_route['action'] = $action;
    }
    
}