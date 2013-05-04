<?php
/*
 * Gestion des chargements de classes / autres
 */

function fileNameByPath($path,$extension = FALSE)
{
    $slashPos = strrpos($path, SEP);
    if($slashPos == FALSE) // Déjà nom d'un fichier
    {
        if(!$extension)
            $path = substr($path, 0, strlen($path) - strlen(EXT));
        return $path;
    }
    
    $fileName = substr($path,   $slashPos + 1);
    
    if(!$extension)
        $fileName = substr($fileName, 0, strlen($fileName) - strlen(EXT));
    
    return $fileName;
}

class Loader
{
    // Instance unique de la classe
    private static $instance     = NULL;
    // Tableau de tous les objets instanciés
    private        $_objects     = array();
    // Tableau des différents fichiers déjà inclus pour ne pas les re-inclure
    private        $_includes    = array();
    
    public static function &getInstance()
    {
        if(!self::$instance)
            self::$instance = new self;
        
        return self::$instance;
    }
    
    public function getFile($filePath)
    {
        $filePath = str_replace(array('application', 'system'),
                                array(APP_DIR, SYS_DIR),
                                $filePath);
        $filePath = BASE_PATH . SEP . $filePath;
        // Déjà inclu
        if(array_search($filePath, $this->_includes) !== FALSE)
            return TRUE;
        
        if(file_exists($filePath))
        { 
            include $filePath;
            $this->_includes[] = $filePath;
            return TRUE;
        }
        
        throw new Exception('<b>' . __CLASS__ . '</b> : Fichier <b>' . $filePath .'</b> introuvable ! ');
        return FALSE;
    }
    
    public function &instanciate($args1 = NULL, $args2 = NULL)
    {
        $className = fileNameByPath($args1);
        
        if(!class_exists($className))
        {        
            if(array_search(BASE_PATH . SEP . $args1, $this->_includes) !== FALSE)
                throw new Exception('<b>' . __CLASS__ .'</b> : <b>'. $className . EXT . '</b> inclut mais classe <b>'. $className .'</b> inexistante ! Les classes doivent porter le même nom que leur fichier !');
            else
                if($this->getFile($args1))
                    return $this->instanciate($args1, $args2);
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
       
            $obj = $args != NULL ? new $className($args) : new $className;
            if(!isset($this->_objects[$className]))
                $this->_objects[$className] = array();
            
            $this->_objects[$className][] =& $obj;
            
            return $obj;   
        }
    }
    
    public function &get($class, $nInstance)
    {
        if(isset($this->_objects[$class]))
        {
            return $this->_objects[$class][$nInstance];
        }
        
    }
}