<?php

/*
* Fonctions de contrôle du statut de l'utilisateur courant : testent 
* l'objet DonkeyUser stocké en session
* Impliquent donc que le module courant utilise DonkeyUser et qu'il soit stocké en session
*/

function session(DonkeyUser $val = NULL)
{
    if($val != NULL)
    {
        $_SESSION[DATASDONKEY]['userSession'] = $val;
        return $val;
    }

    if(!empty($_SESSION[DATASDONKEY]['userSession']))
        return $_SESSION[DATASDONKEY]['userSession'];
    else
        return NULL;
}

function isAuthentified()
{
    return session() && session()->isAuthentified();
}

function isAdmin()
{
    return isAuthentified() && session()->donkeyAdmin;
}