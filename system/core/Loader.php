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
 * Gestion des chargements de classes / autres
 */

class Loader extends Singleton
{
    // Tableau de tous les objets instanciés
    private        $_objects     = array();
    // Tableau des différents fichiers déjà inclus pour ne pas les re-inclure
    private        $_includes    = array();
    
    protected function __construct()
    {
        set_exception_handler('exception_handler');
        self::autoloads();
    }
    
    public static function autoloads()
    {
        require 'SystemHelper.php';
        require 'Lang.php';
        Lang::loadTranslations('system', 'system/langs');
    }
    
    public function getFile($filePath, $returnContent = FALSE, $throw = TRUE)
    {   
        // On ajoute l'extension par défaut si aucune n'est renseignée
        $filePath = appendExt(parsePath($filePath));
        $filePath = BASE_PATH . SEP . $filePath;
        
        // Déjà inclu
        if(array_search($filePath, $this->_includes) !== FALSE)
            return TRUE;
        
        if(file_exists($filePath))
        {
            $this->_includes[] = $filePath;
            if($returnContent)
                return include $filePath;
            
            include $filePath;
            return TRUE;
        }
        
        if($throw)
            throw new Exception('<b>' . __CLASS__ . '</b> : Fichier <b>' . $filePath .'</b> introuvable ! ');
        return FALSE;
    }
    
    public function &instanciate($args1, $args2 = NULL, $throw = TRUE)
    {   
        $args1 = parsePath($args1);
        // On ajoute l'extension par défaut si aucune n'est renseignée
        $args1 = appendExt($args1);
        
        $className = basename($args1, EXT);
        
        if(!class_exists($className))
        {        
            if(array_search(BASE_PATH . SEP . $args1, $this->_includes) !== FALSE)
                throw new Exception('<b>' . __CLASS__ .'</b> : <b>'. $className . '</b> inclut mais classe <b>'. $className .'</b> inexistante ! Les classes doivent porter le même nom que leur fichier !');
            else
                if($this->getFile($args1, FALSE, $throw))
                    return $this->instanciate($args1, $args2);
            else
                return FALSE;
                   
        }
        else
        {
            if(func_num_args() == 2)
                $args = $args2;
            else
            {
                $args = func_get_args();
                array_shift($args);
            }
       
            if(!is_subclass_of($className, 'Singleton')) 
                $obj = $args != NULL ? new $className($args) : new $className; 
            else // Instanciation par singleton 
                $obj = $args != NULL ? $className::instance($args) : $className::instance();
            
            if(!isset($this->_objects[$className]))
                $this->_objects[$className] = array();
            
            $this->_objects[$className][] =& $obj;
            
            return $obj;
        }
    }
    
    public function &get($class, $nInstance = 0)
    {
        if(isset($this->_objects[$class]))
        {
            return $this->_objects[$class][$nInstance];
        }
        else
        {
            throw new Exception('<b>' . __CLASS__ .'</b> : Demande de la classe <b> ' . $class .'</b> non instanciée.');
        }
    }
}