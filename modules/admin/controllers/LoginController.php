<?php
namespace AdminModule;

class LoginController extends \Controller
{

	public function __construct($module)
	{
		parent::__construct($module);

	}

	public function index()
	{
		if(isAuthentified())
			return;

		$this->view('login.php');
	}

	public function login()
	{

	}

	public function onlyAdmin()
	{

	}
}