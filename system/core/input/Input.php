<?php

/*
 * Classe Input
 * Gère l'accès aux données entrantes (GET, POST, FILES , etc ...) 
 */

abstract class Input
{
    /*
     * Tableau des différents supports de données entrantes
     * Ex.: GET, POST, FILES, etc ...
     */
    public static $devices = array();
    
    /*
     * Inclue les classes des différents supports
     */
    public static function loadDevices()
    {
        include 'DeviceInterface.php';
        include 'GetDevice.php';
    }
    
    /*
     * Enregistre une closure gérant un nouveau type de support
     */
    public static function registerInputDevice($deviceName, Closure $c)
    {
        self::$devices[$deviceName] = function($k) use($c)
        {
            return $c($k);
        };
    }
    
    /*
     * Accède à une donnée entrante via les closures
     */
    public static function get($k, $device)
    {
        $device = self::$devices[$device];
        $data = $device($k);
       
        return $data;
    }
}