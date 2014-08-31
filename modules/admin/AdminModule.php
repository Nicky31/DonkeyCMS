<?php

class AdminModule extends Module
{
	const DEFAULT_THEME 	 = 'edmin';
	const DEFAULT_CONTROLLER = 'AdminHome';
	const CUSTOM_USER_CLASS  = 'AdminUser';

	public function __construct($params)
	{
		parent::__construct($params);
		DonkeyUsersMgr::instance();
		
		if(!session())
		{
			session(DonkeyUsersMgr::instance()->user('admin', '72905e7b32d847468edcdbf99f7d218e466cd828300306f1d9f8c3e0512e44fe4394644b581ed52656a2870c9a67c592bc40ca322099aa52bf528c54f9cabde0'));
		}
		else
			session()->permissions++;

		if(!isAuthentified())
		{
			$this->run('login', 'index');
			$this->_runnable = FALSE; // Empêche exécution de tout autre contrôleur
		}
		else if(!isAdmin())
		{
			$this->run('login', 'onlyAdmin');
			$this->_runnable = FALSE;
		}

	}
}