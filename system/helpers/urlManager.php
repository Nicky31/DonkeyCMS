<?php
/*
 * Détermine des urls complètes vers différentes ressources de module
 */

/*
 * Quelques alias de méthodes classe Finder
 */

function imgUrl() 
{
    return call_user_func_array('Finder::imageUrl', func_get_args());
}

function img()
{
    return '<img src="'. call_user_func_array('Finder::imageUrl', func_get_args()) .'" />';   
}

function css() 
{
    return '<link rel="stylesheet" type="text/css" href="'. call_user_func_array('Finder::cssUrl', func_get_args()) .'" />';
}

function js() 
{
    return '<script src="'. call_user_func_array('Finder::jsUrl', func_get_args()) .'"></script>';
}

function redirect($url = '', $time = 0)
{
    // On reste sur le site
    if((is_string($url) && substr($url, 0, 4) != 'http') || (is_array($url) && substr($url['module'], 0, 4) != 'http')) 
    {
        if(is_array($url))
            $url = siteUrl($url, (isset($url['gets']) ? $url['gets'] : array()));
        else
            $url = siteUrl($url);
    }
    
    return '<meta http-equiv="refresh" content="'. $time .';URL='. $url .'">';
}

function refresh($keepVars = FALSE)
{
    $route = array(
        'module'     => MAIN_MODULE,
        'controller' => MAIN_CONTROLLER,
        'action'     => MAIN_ACTION
    );
   
    return redirect($route);
}

function siteUrl($module = array(), $gets = array())
{       
    if(!is_array($module) && !empty($module))
    {
        $moduleExplode = explode('/', $module);
        unset($module);
        $module['module'] = $moduleExplode[0];
        
        if(isset($moduleExplode[1]))
        {
            $module['controller'] = $moduleExplode[1];
        }
        
        if(isset($moduleExplode[2]))
        {
            $module['action'] = $moduleExplode[2];
        }
    }
    
    if(!empty($gets))
    {
        $module['args'] = $gets;
    }
    
    return Router::getRouteStr($module);
}

function url($text, $module = array(), $gets = array())
{   
    echo '<a href="' . siteUrl($module, $gets) . '">' . htmlentities($text) . '</a>';
    return '';
}