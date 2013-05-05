<?php

/*
 * Interface d'un Singleton
 * Permet au loader de détecter les singleton et de contraindre à respecter la structure d'un singleton
 */

interface Singleton
{
    public static function &instance($args = NULL);
}