<?php

class AdminUser implements IUserDataClass
{
	public $permissions = 0;

	public static function datasTableName()
	{
		return 'admin_users';
	}

	public function columnsBinds()
	{
		return array(
			'permissions' => $this->permissions
		);
	}

	public function __construct($datas)
	{
		if(empty($datas))
			return;

		$this->permissions = $datas['permissions'];
	}
}