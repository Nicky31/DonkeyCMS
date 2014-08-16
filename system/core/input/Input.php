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
    public static $_devices = array();
    /*
     * Configuration de la sécurité
     */
    public static $_config  = array();
    
    /*
     * Inclue les classes des différents supports
     */
    public static function init()
    {
        // On récupère le tableau de config et non l'objet pour ne pas que la classe Config lance des exceptions non désirées
        self::$_config = ConfigMgr::instance()->loadConfig('inc/config/security_config', 'securityConfig')->content();
        
        // Chargement des classes handlers
        include 'BasicDevice.php';
    }
    
    /*
     * Enregistre une closure gérant un nouveau type de support
     */
    public static function registerInputDevice($deviceName, Closure $c)
    {
        if(isset(self::$_devices[$deviceName]))
        {
            return TRUE;
        }
        
        self::$_devices[$deviceName] = $c;
        return TRUE;
    }
    
    /*
     * Accède à une donnée entrante via les handlers
     */
    public static function get($k, $device, $options = array())
    {
        if(!isset(self::$_devices[$device]))
        {
            throw new DkException('input.inexistant_device', $device);
        }
        
        $device = self::$_devices[$device];       
        return $device($k, $options);
    }
}