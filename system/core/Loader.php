<?php
/*
 * Gestion des chargements de classes / autres
 */

function fileNameByPath($path,$extension = FALSE)
{
    $slashPos = strrpos($path, SEP);
    if($slashPos == FALSE) // Déjà nom d'un fichier
        return fileNameByPath (SEP . $path);
    
    $fileName = substr($path,   $slashPos + 1);
    
    // Si on doit couper l'extension et qu'il y en a bien une
    if(!$extension && strrpos($fileName, '.') !== FALSE)
        $fileName = substr($fileName, 0, strlen($fileName) - strlen(EXT));
    
    return $fileName;
}

class Loader implements Singleton
{
    // Instance du loader
    private static $_instance    = NULL;
    // Tableau de tous les objets instanciés
    private        $_objects     = array();
    // Tableau des différents fichiers déjà inclus pour ne pas les re-inclure
    private        $_includes    = array();
    
    public static function &instance($args = NULL)
    {
        if(!self::$_instance)
            if($args != NULL)
                self::$_instance = new self($args);
            else
                self::$_instance = new self;
        
        return self::$_instance;
    }
    
    public function getFile($filePath, $returnContent = FALSE)
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
            $this->_includes[] = $filePath;
            if($returnContent)
                return include $filePath;
            
            include $filePath;
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
       
            if(array_search('Singleton',class_implements($className)) === FALSE)
                $obj = $args != NULL ? new $className($args) : new $className;
            else // Instanciation par singleton
                $obj = $args != NULL ? $className::instance($args) : $className::instance();
            
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