<?php
/*
 * Gestion des $_GET
 */

/*
 * On enregistre le support
 */
Input::registerInputDevice('GET', function($k)
{
    return GetDevice::processRequest($k);
});

class GetDevice implements DeviceInterface
{
    public static function processRequest($k)
    {
        return self::getData($k);
    }
    
    public static function getData($k)
    {
        $v = (isset($_GET[$k])) ? $_GET[$k] : FALSE;
        
        return $v;
    }
    
    public static function secureData($v)
    {
        
    }
}