<?php

/*
 * Gestion des caches
 */

class Cache
{
    // Drivers
    const FILE_DRIVER    = 0,
          DB_DRIVER      = 1,
          DEFAULT_DRIVER = self::FILE_DRIVER;

    // Contenu du cache
    private $_content  = array();
    // Nom du cache
    private $_name     = NULL;
    // Support du cache (Db ou file ?)
    private $_driver   = NULL;

    public function __construct($cacheName = FALSE, $driver = self::DEFAULT_DRIVER)
    {
        $this->_content = array(
            'datas'     => FALSE,
            'startTime' => FALSE,
            'liveTime'  => FALSE
        );
        $this->loadDriver($driver);

        if($cacheName)
        {
            $this->loadCache($cacheName);
        }
    }

    public function loadDriver($driver = self::DEFAULT_DRIVER)
    {
        switch($driver)
        {
            case self::FILE_DRIVER :
                $this->_driver = new FileDriver;
            break;
            case self::DB_DRIVER :
                $this->_driver = new DbDriver;
            break;
            default:
                $this->loadDriver(); // On charge celui par défaut
            break;
        }

        return TRUE;
    }

    public function loadCache($cacheName)
    {
        $this->_name = $cacheName;
        // Cache demandé existant
        if(($cacheContent = $this->_driver->getCache($cacheName)) !== FALSE)
        {   
            // Cache toujours valable
            if(($cacheContent['startTime'] + $cacheContent['lifeTime']) > time())
            {
                $this->_content = $cacheContent;
                return TRUE;
            } 
            else
            {
                $this->_driver->removeCache($cacheName);
            }
        }

        // Cache inexistant ou périmé
        return FALSE;
    }

    // Cache encore vierge ? 
    public function isNew()
    {
        return $this->_content['datas'] === FALSE;
    }

    // Sauvegarde le cache en éditant si nécessaire 
    public function save($lifeTime, $datas)
    {           
        $this->_content = array(
            'datas'     => $datas,
            'startTime' => time(),
            'lifeTime'  => $lifeTime
        );

        $this->_driver->addCache($this->_name, $this->_content);
        return $this;
    }

    public function getDatas()
    {
        return $this->_content['datas'];
    }
}