<?php

/*
 * Classe Router centralisant les traitements de route
 */

abstract class Router
{
    /*
     * Données de routage utilisées par défaut lorsque non renseignées ou introuvable par le Router
     * ['module']     = module sélectionné par défaut
     * ['controller'] = controller du module utilisé par défaut
     * ['action']     = méthode du controller appelée par défaut
     */
    public static $_defaultRoute = array();
    
    /*
     * Retourne un tableau de données extraites de l'uri suivant un pattern
     * Uri Pattern :
     * yourwebsite.com/Module/Controller/Action-arg1-arg2.html
     */
    public static function getRouteArray($strRoute)
    {
        $route = self::$_defaultRoute;
        
        if(empty($strRoute))
        {
            return $route;
        }
        
        $datas = explode('/', $strRoute);
        // Module
        if(!empty($datas[0]))
        {
            $route['module'] = $datas[0];
        }
        
        // Controller
        if(!empty($datas[1]))
        {
            $route['controller'] = $datas[1];
        }
        
        // Action + Params
        if(!empty($datas[2]))
        {
            if(URIEXT != '' && $extPos = strrpos($datas[2], URIEXT))
            {
                $datas[2] = substr($datas[2], 0, $extPos); // On enlève l'extension
            }
            
            $actionExplode = explode('-', $datas[2]);
            $route['action'] = array_shift($actionExplode);
            
            if(isset($actionExplode[0])) // args spécifiés
            {
                $route['args'] = $actionExplode;
            }
        }
        
        return $route;
    }
    
   /*
    * Retourne les données renseignées dans l'url à partir du répertoire racine du CMS
    */
    public static function getPathInfo()
    {
        if(!isset($_SERVER['PATH_INFO']))
        {
            return '';    
        }
        else
        {
            return trim($_SERVER['PATH_INFO'], '/');
        }
    }
    
    /*
     * Retourne une url à partir de données
     */
    public static function getRouteStr($route = array())
    {
        $urlStr = BASE_URL;
        
        if(!isset($route['module']))
        {
            return $urlStr;
        }
        $urlStr .= '/'. $route['module'];
        
        if(!isset($route['controller']))
        {
            return $urlStr;
        }
        $urlStr .= '/'. $route['controller'];
        
        if(!isset($route['action']))
        {
            /*
             * Possibilité d'omettre l'action mais de renseigner des args pour les indiquer avec l'action par défaut
             */
            if(isset($route['args']))
            {
                $urlStr .= '/' . self::$_defaultRoute['action'] . '-' . implode('-', (array)$route['args']) . URIEXT;
            }
            return $urlStr;
        }
        $urlStr .= '/'. $route['action'];
        
        if(!isset($route['args']))
        {
            return $urlStr . URIEXT;
        }
        
        // Chemin complet renseigné dans l'array
        return $urlStr . '-' . implode('-', (array)$route['args']) . URIEXT;
    }
}