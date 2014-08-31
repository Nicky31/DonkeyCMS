<?php

class DonkeyModulesModel extends Model
{
    public function allModules($onlyEnabled = FALSE)
    {
        $where = $onlyEnabled ? ' WHERE enabled = 1 ' : '';
        $query = $this->_db->query('SELECT * FROM donkey_modules' . $where);
        return $query->fetchAll();
    }

    public function updateModuleSettings($moduleName, $moduleSettings)
    {
        $query = $this->_db->prepare('UPDATE donkey_modules SET settings = ? WHERE name = ?');
        $query -> bindValue(1, $moduleSettings);
        $query -> bindValue(2, $moduleName);
        return $query -> execute();
    }
}