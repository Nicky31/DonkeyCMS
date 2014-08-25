<?php

class HomeModule extends Module
{  
    public function __construct($params)
    { 
    	parent::__construct($params);
    	$this->registerDatabase('myDb', $this->config()->item('myDb'));
    	Hook::instance()->bind('pre_main_module', array($this, 'hookTest'));
    }

    public function hookTest()
    {
    	echo 'pre_main_module <br />';
    }
}