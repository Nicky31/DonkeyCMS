<?php

/*
 * Support de cache sur fichier
 */

 class FileDriver implements CacheDriverInterface
 {
    const CACHE_DIR = CACHE_DIR;

    public function getCache($name)
    {
        if(file_exists($filePath = BASE_PATH . SEP . self::CACHE_DIR . SEP . appendExt($name)))
        {
            return unserialize(file_get_contents($filePath));
        }

        return FALSE;
    }

    public function cacheExists($name)
    {
        return file_exists(BASE_PATH . SEP . self::CACHE_DIR . SEP . appendExt($name));
    }

    public function removeCache($name)
    {
        if(file_exists($filePath = BASE_PATH . SEP . self::CACHE_DIR . SEP . appendExt($name)))
        {
            unlink($filePath);
            return TRUE;
        }

        return FALSE;
    }

    public function addCache($name, $datas)
    {
        file_put_contents(BASE_PATH . SEP . self::CACHE_DIR . SEP . appendExt($name), serialize($datas));
    }

 }