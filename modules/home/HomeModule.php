<?php

class HomeModule extends Module
{  
    public function __construct($params)
    { 
    	parent::__construct($params);
    	$this->registerDatabase('myDb', $this->config()->item('myDb'));
    }
}