<?php

class ModulesSystemModel extends Model
{
	public function allModules($onlyEnabled = FALSE)
	{
		$where = $onlyEnabled ? ' WHERE enabled = 1 ' : ' ';
		$query = $this->_db->query('SELECT * FROM donkey_modules' . $where);
		return $query->fetchAll();
	}
}