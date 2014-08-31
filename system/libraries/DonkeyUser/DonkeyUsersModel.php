<?php

class DonkeyUsersModel extends Model
{   

    public function getUserDatas($username, $password)
    {
        $query = $this->_db->prepare('SELECT * FROM '. DonkeyUser::datasTableName() .' WHERE username = ? AND password = ?');
        $query -> bindValue(1, $username);
        $query -> bindValue(2, $password);
        $query -> execute();
        return $query->fetch();
    }

    public function getCustomUserDatas($uid, $customTable)
    {
        $query = $this->_db->prepare('SELECT * FROM '. $customTable .' WHERE id = ?');
        $query -> bindValue(1, $uid);
        $query -> execute();
        return $query->fetch();
    }

    public function updateUserDatas($uid, $datasTable, array $datasBinds)
    {
        $query = $this->_db->prepare($this->buildPReq($datasTable, $datasBinds));
        $datasNum = sizeof($datasBinds);
        for($i = 0; $i < $datasNum; ++$i)
        {
            $query->bindValue($i + 1, current($datasBinds));
            next($datasBinds);
        }
        $query->execute();

        return $this->_db->lastInsertId();
    }

    private function buildPReq($datasTable, array $datasBinds)
    {
        return 'REPLACE INTO '. $datasTable .' ('. implode(',', array_keys($datasBinds)) .
               ') VALUES ('. implode(',', array_fill(0, sizeof($datasBinds), '?')) . ')';
    }
}