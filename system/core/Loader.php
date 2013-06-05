<?php
/*
 * Gestion des chargements de classes / autres
 */

class Loader extends Singleton
{
    // Tableau de tous les objets instanciés
    private        $_objects     = array();
    // Tableau des différents fichiers déjà inclus pour ne pas les re-inclure
    private        $_includes    = array();
    
    public static function autoloads()
    {
        
        
    }
    
    private static function fileNameByPath($path,$extension = FALSE)
    {
        $slashPos = strrpos($path, SEP);
        if($slashPos == FALSE) // Déjà nom d'un fichier
            return self::fileNameByPath (SEP . $path);
        $fileName = substr($path,   $slashPos + 1);

        // Si on doit couper l'extension et qu'il y en a bien une
        if(!$extension && strrpos($fileName, '.') !== FALSE)
            $fileName = substr($fileName, 0, strlen($fileName) - strlen(EXT));

        return $fileName;
    }

    
    public function getFile($filePath, $returnContent = FALSE, $throw = TRUE)
    {   
        $filePath = str_replace(array('inc', 'system', 'modules', '/', '\\'),
                                array(INC_DIR, SYS_DIR, MODS_DIR, SEP, SEP),
                                $filePath);
        // On ajoute l'extension par défaut si aucune n'est renseignée
        if(strrpos(self::fileNameByPath($filePath, TRUE), '.') === FALSE)
            $filePath .= EXT;

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
        $args1 = str_replace(array('/','\\'),
                             array(SEP, SEP), 
                             $args1);
        // On ajoute l'extension par défaut si aucune n'est renseignée
        if(strrpos(self::fileNameByPath($args1, TRUE), '.') === FALSE)
                $args1 .= EXT;
        $className = self::fileNameByPath($args1);
        
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
            //var_dump(func_get_args());
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