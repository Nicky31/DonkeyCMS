<?php

/*
 * Retourne le chemin après avoir remplacé les éléments modifiables : 
 * nom des principaux dossiers, séparateurs. 
 */
function parsePath($path)
{
    $filePath = str_replace(array('inc', 'system', 'modules', '/', '\\'),
                            array(INC_DIR, SYS_DIR, MODS_DIR, SEP, SEP),
                            $path);  
    return $filePath;
}

/*
 * Ajoute l'extension au chemin/fichier si elle n'est pas indiquée
 */
function appendExt($path, $ext = EXT)
{
    if(strrpos(basename($path), $ext) === FALSE)
    {
        return $path . $ext;
    }
    
    return $path;
}

/*
 * Raccourci de Lang::tr
 */

function tr($name, $args = array())
{
    return Lang::tr($name, $args);
}

/*
 * Raccourci de echo Lang::tr
 */
function pTr($name, $args = array())
{
    echo tr($name, $args);
}

/* 
 * Renvoit l'ip du visiteur
 * THX InfiniteCMS
 */
function getIp()
{
    //cloudflare
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
        return $_SERVER['HTTP_CF_CONNECTING_IP'];

    if (isset($_SERVER['REMOTE_ADDR']))
        return $_SERVER['REMOTE_ADDR'];

    if (isset($_SERVER['HTTP_X_REQUESTED_FOR'])
    && false !== filter_var($_SERVER['HTTP_X_REQUESTED_FOR'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_X_REQUESTED_FOR'];
    
    return null;
}

/*
 * Débug fonction
 * Retourne la valeur d'une variable au sein d'une balise <pre>
 */
function dbg($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre> <br />';
}

/*
 * Retourne le nom du thème du module spécifié
 */

function getTheme($module)
{
    return Donkey::instance()->module($module)->getTheme();
}