<?php

class DonkeyUsersMgr extends Singleton
{
	protected $_usersModel 		= NULL;
	// Classe user personnalisée du module maître courant
	protected $_customUserClass = NULL;

	protected function __construct()
	{
		$modelName = 'DonkeyUsers'. Model::SUFFIX;
		$this->_usersModel = new $modelName('donkeyDb');
		Loader::instance()->getFile(__DIR__ . DIRECTORY_SEPARATOR . 'SessionHelper.php');

		$customUsrClassConstant = ucfirst(MAIN_MODULE) . Module::SUFFIX . '::CUSTOM_USER_CLASS';
		$this->_customUserClass = defined($customUsrClassConstant) ? constant($customUsrClassConstant) : NULL;
	}

	public function customUserClass()
	{
		return $this->_customUserClass;
	}

	/*
	 * Renvoit un objet DonkeyUser hydraté avec les données de base associées à username +
 	 * le customUser()
	 */
	public function user($username, $password)
	{
		return new DonkeyUser($this->_usersModel->getUserDatas($username, $password));
	}

	/*
	 * Si le module maître a défini une constante CUSTOM_USER_CLASS, instancie et retourne
	 * cette classe en lui fournissant les données de l'utilisateur dans la table CUSTOM_USER_CLASS::datasTableName()
	 */
	public function customUser($uid)
	{
		$customUserClass =& $this->_customUserClass;
		if(empty($customUserClass))
			return NULL;

		return new $customUserClass($this->_usersModel->getCustomUserDatas($uid, $customUserClass::datasTableName()));
	}

	/*
	 * Met jour les lignes relatives à l'objet $user
	 */
	public function updateUser(DonkeyUser $user)
	{
		$this->_usersModel->updateUserDatas($user->id,
											DonkeyUser::datasTableName(), 
											array('id' => $user->id) + $user->columnsBinds());

		if($customUser = $user->customUser())
		{
			$this->_usersModel->updateUserDatas(
								$user->id, 
								call_user_func(get_class($customUser) .'::datasTableName'), 
								array('id' => $user->id) + $customUser->columnsBinds());
		}
	}
}