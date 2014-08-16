<?php

/*
 * Légère abstraction à l'utilisation de l'autoloader \Doctrine2\ClassLoader.php
 */

class Loader extends Singleton
{
    /*
     * Classes existantes au sein du projet (& ses sous dossiers)
     * array[className] = 'filePath';
     * Defini par system/libraries/ClassIndexer.php dans l'index
     */
    private $_classes  = array();

    /**
     * $classes tableau associatif
     * $classes['NomDeClasse'] = 'cheminFichier'
     */
    protected function __construct($classes = array())
    {
        $this->_classes = $classes;
        spl_autoload_register(array($this, 'loadClass'));
        set_exception_handler(array($this, 'loadExceptionMgr'));
    }

    /*
     * Permet de ne charger le fichier ExceptionManager que si une exception est levée
     */
    public function loadExceptionMgr($e)
    {
        ExceptionManager::handleException($e);
    }

    public function autoloads()
    {   
        require BASE_PATH . SEP . SYS_DIR . SEP . 'core/SystemHelper.php';
        // Traductions de base du système
        Lang::loadTranslations('system', 'system/langs');

        // Initialisation du Finder
        Finder::init(BASE_PATH, BASE_URL, array(
            'lib'    => array('modules/%s/libraries/', 'inc/libraries/', 'system/libraries/'),
            'helper' => array('modules/%s/helpers/', 'inc/helpers/', 'system/helpers/'),
            'config' => array('modules/%s/config/', 'inc/config/'),
            'lang'   => array('modules/%s/langs/', 'inc/langs/', 'system/langs/'),
            'view'   => array('modules/%s/themes/%s/views/'),
            'layout' => array('modules/%s/themes/%s/'),
            'image'  => array('modules/%s/themes/shared/images', 'modules/%s/themes/%s/images'),
            'css'    => array('modules/%s/themes/shared/css', 'modules/%s/themes/%s/css'),
            'js'     => array('modules/%s/themes/shared/js', 'modules/%s/themes/%s/js')
        ));

        // Configurations système
        ConfigMgr::instance()->loadConfig('inc/config/sys_config', 'sysConfig');
        ConfigMgr::instance()->loadConfig('inc/config/databases_config', 'dbsConfig');
        ConfigMgr::instance()->loadConfig('inc/config/autoloads_config','autoloadsConfig');
    }

    public function getFile($filePath, $returnContent = FALSE, $force = FALSE)
    {   
        // On ajoute l'extension par défaut si aucune n'est renseignée
        $filePath = appendExt(parsePath($filePath));

        if(strpos($filePath, BASE_PATH . SEP) === FALSE)
            $filePath = BASE_PATH . SEP . $filePath;
        
        // Déjà inclu
        if(array_search($filePath, get_included_files()) !== FALSE && !$force)
            return TRUE;
        
        if(file_exists($filePath))
        {
            if($returnContent)
                return include $filePath;
            
            include $filePath;
            return TRUE;
        }
        
        throw new DkException('file.unfoundable', $filePath);
    }

    public function fileExists($filePath)
    {
        $filePath = appendExt(parsePath($filePath));
        if(strpos($filePath, BASE_PATH . SEP) === FALSE)
            $filePath = BASE_PATH . SEP . $filePath;

        return file_exists($filePath);
    }

    public function loadClass($className)
    {
        // Classe inexistante
        if(!$this->classExists($className))
        {
            throw new DkException('load_class.inexistant', $className);
        }

        if(file_exists($this->_classes[$className]))
        {
            require $this->_classes[$className];
        }
        else
        {
            throw new DkException('classes_registry.corrupted', $className);
        }
    }

    public function classExists($className)
    {
        return array_key_exists($className, $this->_classes);
    }
}