<?php
/*
 * Gestion des tableaux de données entrantes
 */

/*
 * On enregistre le support ...
 */        
Input::registerInputDevice('GET', function($k, $options = array())
{
    return BasicDevice::processGetRequest($k, $options);
});
Input::registerInputDevice('POST', function($k, $options = array())
{
    return BasicDevice::processPostRequest($k, $options);
});
Input::registerInputDevice('COOKIE', function($k, $options = array())
{
    return BasicDevice::processCookieRequest($k, $options);
});

abstract class BasicDevice
{
    
    public static function processGetRequest($k, $options = array())
    {
        $val = self::getData($k, Router::getCurRouteParams(), $options);
        return ($val != NULL) ? self::secureData(array($k, $val), 'GET', $options) : NULL;
    }
    
    public static function processPostRequest($k, $options = array())
    {
        $val = self::getData($k, $_POST, $options);
        return ($val != NULL) ? self::secureData(array($k, $val), 'POST', $options) : NULL;
    }


    public static function processCookieRequest($k, $options = array())
    {
        $val = self::getData($k, $_COOKIE, $options);
        return ($val != NULL) ? self::secureData(array($k, $val), 'COOKIE', $options) : NULL;
    }
    
    public static function getData($k, $array, $options = array())
    {
        return (isset($array[$k])) ? $array[$k] : NULL;
    }
    
    public static function secureData($data, $deviceName, $options = array())
    {
        $data[1] = trim(urldecode($data[1]));

        $configGetter = function($k) use ($data, $deviceName, $options)
        {
            if(!isset($options[$k]))
                if(isset(Input::$_config[$deviceName]['dedicated'][$data[0]][$k]))
                    return Input::$_config[$deviceName]['dedicated'][$data[0]][$k];
                else if(isset(Input::$_config[$deviceName][$k]))
                    return Input::$_config[$deviceName][$k];
                else
                    return FALSE;
            else
                return $options[$k];
        };

        if($varType = $configGetter('varType'))
        {
            switch($varType) :
                case 'int' :
                    if(!is_numeric($data[1]))
                        return FALSE;
                break;
                case 'bool' :
                    if(!in_array($data[1], array('1', 'true', '0', 'false', 'enable', 'disable')))
                        return FALSE;
                break;
                case 'all' : 
                break;
                default:
                    throw new DkException('input.basicDevice.bad_varType', $varType);
                break;
            endswitch;
        }

        if(($maxSize = $configGetter('maxSize')) !== FALSE)
        {
            if(is_numeric($data[1]))
            {
                if((int)$data[1] > $maxSize)
                    return FALSE;
            }
            else if(is_string($data[1]))
            {
                if(strlen($data[1]) > $maxSize)
                    return FALSE;
            }
        }
        
        if($pattern = $configGetter('pattern'))
        {
            return preg_match($pattern, $data[1]) ? $data[1] : FALSE;
        }
        else // Aucune regex renseigné pour ce type de donnée entrante, on la renvoit tel quelle
        {
            return $data[1];
        }
    }
}