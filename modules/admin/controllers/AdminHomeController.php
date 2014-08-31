<?php

class AdminHomeController extends Controller
{
	public function __construct($module)
	{
		parent::__construct($module);
	}

	public function index()
	{
		$this->view('home.php');
	}
}