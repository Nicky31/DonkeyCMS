<?php

/*
 * Interface de tous les supports de données entrantes
 */

interface DeviceInterface
{
    /*
     * Première méthode appellée traitant la demande
     */
    public static function processRequest($k);
    /*
     * Récupère la valeur
     */
    public static function getData($k);
    /*
     * Sécurise la valeur
     */
    public static function secureData($v);
}