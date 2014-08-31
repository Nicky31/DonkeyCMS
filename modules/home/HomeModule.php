<?php

class HomeModule extends Module
{  
    const CUSTOM_USER_CLASS = 'HomeUser';

    public function __construct($params)
    { 
    	parent::__construct($params);
    	// $this->registerDatabase('myDb', $this->config()->item('myDb'));
    	Hook::instance()->bind('pre_main_module', array($this, 'hookTest'));

        DonkeyUsersMgr::instance();
        if(!session())
        {
            session(DonkeyUsersMgr::instance()->user('admin', '72905e7b32d847468edcdbf99f7d218e466cd828300306f1d9f8c3e0512e44fe4394644b581ed52656a2870c9a67c592bc40ca322099aa52bf528c54f9cabde0'));
        }        
    }

    public function hookTest()
    {
    	// echo 'pre_main_module <br />';
    }
}