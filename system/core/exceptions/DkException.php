<?php

/*
 * Exception personalisée 
 */

class DkException extends Exception
{
    public function __construct($content, $args = array())
    {
    	// Exception en boucle lors de tr() si on ne charge pas les traductions d'exception dans le constructeur :
    	ExceptionManager::init();

        if(func_num_args() > 2)
        {
            $args = func_get_args();
            $content = array_shift($args);
        }

        parent::__construct(tr($content, $args));
    }
}