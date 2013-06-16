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
    if(strrpos(basename($path), EXT) === FALSE)
    {
        return $path . EXT;
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