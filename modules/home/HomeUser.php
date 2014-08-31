<?php

class HomeUser implements IUserDataClass
{
	public $titre = NULL;

	public function __construct($datas)
	{
		if(empty($datas))
			return;

		$this->titre = $datas['titre'];
	}

	public static function datasTableName()
	{
		return 'home_users';
	}

	public function columnsBinds()
	{
		return array(
			'titre' => $this->titre
		);
	}
}