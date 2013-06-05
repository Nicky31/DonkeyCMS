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

class Donkey extends Singleton
{
    // Instance du loader
    private $_loader    = NULL;
    // Classe config du système
    private $_sysConfig = NULL;
    // Tableau des modules chargés
    private $_modules   = array();
    // Classe Output qui gère la page finale
    private $_output    = NULL;
    
    protected function __construct(&$Loader)
    {
        $this->_loader =& $Loader;

        $ConfigMgr =& $Loader->instanciate('system/core/config/ConfigMgr');
        
        $this->_sysConfig =& $ConfigMgr->loadConfig('inc/config/sys_config', 'sysConfig');
        $this->_sysConfig->toConstants();
        $this->autoloads();
        
        set_exception_handler('exception_handler');
    }
    
    public function run()
    {
        $this->_loader->getFile('system/core/module/Controller');
        $this->_loader->getFile('system/core/module/Module');
        
        $this->_output =& $this->_loader->instanciate('system/core/Output', $this);
        
        $route = $this->route();
        $this->runModule($route['module'], $route['controller'], $route['action']);
        
        $this->_output->render();
    }
    
    public function runModule($module,$controller,$action)
    {
        $moduleDir = MODS_PATH . $module . SEP;
        if(!is_dir($moduleDir))
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : Module <b>'. $module .'</b> inexistant !');
            return;
        }
        
        $params = array(
            'name' => $module,
            'controller' => $controller
        );
        $moduleObj = NULL;
        
        // Une surcharge de la classe module existe
        if(file_exists($moduleDir . ucfirst($module . MODULESUFFIX . EXT)))        
        {
            $moduleObj =& $this->_loader->instanciate('modules/' . $module . '/' . ucfirst($module . MODULESUFFIX), $params);
        }
        else
        { 
            $moduleObj =& $this->_loader->instanciate('Module', $params);
        }
        
        if($moduleObj instanceof Module === FALSE)
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : La classe module <b>'. $module . MODULESUFFIX .'</b> doit hériter de <b>Module</b> !');
            return;
        }
        
        $this->_modules[] =& $moduleObj;   
        $moduleObj->run($action);
    }


    public function route()
    {
        $module = $this->_sysConfig['defaultModule'];
        $controller = $this->_sysConfig['defaultController'];
        $action = $this->_sysConfig['defaultAction'];
        
        if(!empty($_GET[$this->_sysConfig['routeGet']]))
        {
            $route = explode('/',$_GET[$this->_sysConfig['routeGet']]);
            
            if(!empty($route[0]))
                $module = $route[0];
            
            if(sizeof($route) > 1 && !empty($route[1]))
                $controller = $route[1];
           
            if(sizeof($route) > 2 && !empty($route[2]))
                $action = $route[2];
        }

        return array(
            'module' => $module,
            'controller' => $controller,
            'action' => $action
        );
    }
    
    public function &module($target = NULL)
    {
        if($target === NULL)
        {
            return $this->_modules;
        }
        else if(is_numeric($target))
        {
            return $this->_modules[$target];
        }
        else
        {
            foreach($this->_modules as $module)
            {
                if(strtoupper($module->name()) == strtoupper($target))
                {
                    return $module;
                }
            }
        }
    }
    
    public function autoloads()
    {
        $autoloadConfig =& ConfigMgr::instance()->loadConfig('inc/config/autoloads_config','autoloadsConfig');
        foreach($autoloadConfig['helpers'] as $helper)
        {
            $this->_loader->getFile('inc/helpers/' . $helper . EXT, FALSE, FALSE);
            $this->_loader->getFile('system/helpers/' . $helper . EXT, FALSE, FALSE);
        }
    }
    
}