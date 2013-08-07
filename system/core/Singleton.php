<?php

/*
 * Classe Singleton
 * Permet au loader de détecter les singleton et de les implémenter facilement
 */

abstract class Singleton 
{    
    public static function instance($args = NULL)
    { 
        static $instance;
        
        if($instance == FALSE)
            if($args != NULL)
                $instance = new static($args);
            else
                $instance = new static();
        
        return $instance;
    }  
}