<?php
function img_url($img, $isFullPath = FALSE)
 {
    // Premier argument contient le chemin complet en partant du répertoire modules. Permet de laisser la liberté du module/thème
    if($isFullPath)
    {
        return BASE_URL . '/' . MODS_DIR . SEP . $img;
    } 
    
    if(!isset($GLOBALS['curTemplate']))
    {
        throw new Exception('<b>Helper Url Manager</b> : Impossible de déterminer le chemin demandé car superglobable $GLOBALS[\'curTemplate\'] non initialisée.');
        return;
    }
    
    $sharedDir = Donkey::instance()->module($GLOBALS['curTemplate']['module'])->config()->item('sharedTheme');
    // L'image est présente dans le thème partagé du module
    if(file_exists(MODS_PATH . $GLOBALS['curTemplate']['module'] . '/themes/' . $sharedDir . '/images/' . $img))
    {
        return BASE_URL . '/' . MODS_DIR . '/' . $GLOBALS['curTemplate']['module'] . '/themes/' .  $sharedDir . '/images/' . $img;
    }
    
    return BASE_URL . '/' . MODS_DIR . '/' . $GLOBALS['curTemplate']['module'] . '/themes/' .  $GLOBALS['curTemplate']['theme'] . '/images/' . $img;
 }
 
function css($css, $isFullPath = FALSE)
{
    // Premier argument contient le chemin complet en partant du répertoire modules. Permet de laisser la liberté du module/thème
    if($isFullPath)
    {
        return BASE_URL . '/' . MODS_DIR . SEP . $css;
    } 
    
    if(!isset($GLOBALS['curTemplate']))
    {
        throw new Exception('<b>Helper Url Manager</b> : Impossible de déterminer le chemin demandé car superglobable $GLOBALS[\'curTemplate\'] non initialisée.');
        return;
    }
    
    $sharedDir = Donkey::instance()->module($GLOBALS['curTemplate']['module'])->config()->item('sharedTheme');
    // Le css est présent dans le thème partagé du module
    if(file_exists(MODS_PATH . $GLOBALS['curTemplate']['module'] . '/themes/' . $sharedDir . '/css/' . $css . '.css'))
    {
        return BASE_URL . '/' . MODS_DIR . '/' . $GLOBALS['curTemplate']['module'] . '/themes/' .  $sharedDir . '/css/' . $css .'.css';
    }
    
    return BASE_URL . '/' . MODS_DIR . '/' . $GLOBALS['curTemplate']['module'] . '/themes/' .  $GLOBALS['curTemplate']['theme'] . '/css/' . $css .'.css';
}

function js($js, $isFullPath = FALSE)
{
    // Premier argument contient le chemin complet en partant du répertoire modules. Permet de laisser la liberté du module/thème
    if($isFullPath)
    {
        return BASE_URL . '/' . MODS_DIR . SEP . $js;
    } 
    
    if(!isset($GLOBALS['curTemplate']))
    {
        throw new Exception('<b>Helper Url Manager</b> : Impossible de déterminer le chemin demandé car superglobable $GLOBALS[\'curTemplate\'] non initialisée.');
        return;
    }
    
    $sharedDir = Donkey::instance()->module($GLOBALS['curTemplate']['module'])->config()->item('sharedTheme');
    // Le fichier js est présent dans le thème partagé du module
    if(file_exists(MODS_PATH . $GLOBALS['curTemplate']['module'] . '/themes/' . $sharedDir . '/jscripts/' . $js . '.js'))
    {
        return BASE_URL . '/' . MODS_DIR . '/' . $GLOBALS['curTemplate']['module'] . '/themes/' .  $sharedDir . '/jscripts/' . $js .'.js';
    }
    
    return BASE_URL . '/' . MODS_DIR . '/' . $GLOBALS['curTemplate']['module'] . '/themes/' .  $GLOBALS['curTemplate']['theme'] . '/jscripts/' . $js .'.js';
}

 
function img($nom, $alt = '')
{
    return '<img src="' . img_url($nom) . '" alt="' . $alt . '" />';
}

function redirect($url = '', $time = 0)
{
    // On reste sur le site
    if((is_string($url) && substr($url, 0, 4) != 'http') || (is_array($url) && substr($url['module'], 0, 4) != 'http')) 
    {
        if(is_array($url))
            @$url = site_url($url['module'], $url['gets']);
        else
            $url = site_url($url);
    }
    
    return '<meta http-equiv="refresh" content="'. $time .';URL='. $url .'">';
}

function refresh()
{
    return redirect($_GET['p']);
}

function site_url($module = '', $gets = '')
{		
    if(is_array($module) && !empty($module))
    {
        $module = $module['module'];
        @$gets = $module['gets'];
    }
    
    $stringUrl = BASE_URL .'/index.php';	
		
    $stringUrl .= '?' . ROUTEGET . '=' . $module;
    if($gets != '')
        $stringUrl .= '&'. $gets;
    
    return $stringUrl;
}

function url($text, $module = '', $gets = '')
{	
    echo '<a href="' . site_url($module, $gets) . '">' . htmlentities($text) . '</a>';
    return '';
}