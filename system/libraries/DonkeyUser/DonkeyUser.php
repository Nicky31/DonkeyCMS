<?php

/*
 * Classe encapsulant les données d'un utilisateur
 * Contiendra toujours les données de base de la table donkey_users 
 * Additionnellement, les différents modules peuvent coder leur propre classe utilisateur
 * contenant d'autres informations personalisées et situées sur une table propre au module
 * DonkeyUser sera alors composé de cette instance et permettra son accès comme si DonkeyUser était la classe mère
 *
 */

class DonkeyUser implements IUserDataClass
{
    // Objet modifié depuis sa création
    protected $_overwritten   = FALSE;
    // Objet user encapsulant des données supplémentaires propres à chaque module
    protected $_customUser    = NULL;

    protected $id 		      = -1;
    protected $username       = NULL;
    protected $password       = NULL;
    // Droits d'administration sur Donkey ? 
    protected $donkeyAdmin    = FALSE;
    // E-mail
    protected $email          = NULL;
    // Dernière ip avec laquelle l'utilisateur a visité une page
    protected $lastIp         = NULL;	
    // Timestamp de création de l'utilisateur
    protected $creationTime   = 0;
    // Timestamp de dernière visite de l'utilisateur
    protected $lastActiveTime = 0;

    public function __construct($datas)
    {   
        if(empty($datas))
            return;

        $this->id = $datas['id'];
        $this->username = $datas['username'];
        $this->password = $datas['password'];
        $this->donkeyAdmin = $datas['donkey_admin'];
        $this->email = $datas['email'];
        $this->lastIp = $datas['last_ip'];
        $this->creationTime = $datas['creation_time'];
        $this->lastActiveTime = $datas['lastactive_time'];

        $this->_customUser = DonkeyUsersMgr::instance()->customUser($this->id);
    }

    public function __destruct()
    {
        if($this->_overwritten && $this->isAuthentified())
        { 
            DonkeyUsersMgr::instance()->updateUser($this);
        }
    }

    public static function datasTableName()
    {
        return 'donkey_users';
    }

    public function customUser()
    {
        return $this->_customUser;
    }    

    public function __get($key)
    {
        if(isset($this->_customUser->$key))
            return $this->_customUser->$key;

        return $this->$key;
    }

    public function __set($key, $val)
    {
        switch ($key)
        {
            case 'username':
                $this->username = $val;
                break;
            
            case 'email':
                $this->email = $val;
                break;

            case 'lastIp':
                $this->lastIp = $val;
                break;

            case 'lastActiveTime':
                $this->lastActiveTime = $val;
                break;

            default:
                $this->_customUser->$key = $val;
                break;
        }

        $this->_overwritten = TRUE;
    }

    public function __call($method, $params)
    {
        return call_user_func_array(array($this->_customUser, $method), $params);
    }

    public function __wakeup()
    {
        if(!$this->isAuthentified())
        {
            $this->_customUser = NULL;
            return;
        }

        $customUserClass = DonkeyUsersMgr::instance()->customUserClass();
        if(get_class($this->_customUser) === $customUserClass)
            return;

        $this->_customUser = NULL;
        if(empty($customUserClass))
            return;
        // Si customUser n'est pas la classe indiquée par l'actuel module maître, on le recharge
        $this->_customUser = DonkeyUsersMgr::instance()->customUser($this->id);
    }

    public function isAuthentified()
    {
        return $this->username != NULL;
    }

    public function columnsBinds()
    {
        return array(
            'username' => $this->username,
            'password' => $this->password,
            'donkey_admin' => $this->donkeyAdmin,
            'email' => $this->email,
            'last_ip' => $this->lastIp,
            'creation_time' => $this->creationTime,
            'lastactive_time' => $this->lastActiveTime
        );
    }

}