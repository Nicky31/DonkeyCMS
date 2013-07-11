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
            throw new Exception('<b>'. __CLASS__ .'</b> : Enregistrement d\'un support <b>'. $deviceName .'</b> déjà enregistré !');
            return;
        }
        
        self::$_devices[$deviceName] = $c;
    }
    
    /*
     * Accède à une donnée entrante via les handlers
     */
    public static function get($k, $device, $options = array())
    {
        if(!isset(self::$_devices[$device]))
        {
            throw new Exception('<b>'. __CLASS__ .'</b> : Support <b>'. $device .'</b> inexistant ou non-enregistré.');
            return;
        }
        
        $device = self::$_devices[$device];       
        return $device($k, $options);
    }
}