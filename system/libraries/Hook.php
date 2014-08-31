<?php

class Hook extends Singleton
{
	// $_hooks['hookName'] = array(callable type1, type2 ...)
	public $_hooks = array();

	/*
	 * Exécute toutes les fonctions associées à $hookName 
	 * Fournit tous les paramètres après $hookName en paramètre de ces fonctions
	 * Renvoit un tableau contenant la liste des paramètres (sauf $hookName) 
	 * dans le même ordre avec lequel ils ont été fournis
	 */
	public function exec($hookName)
	{
		$params = func_get_args(); 
		array_shift($params); 

		if(empty($this->_hooks[$hookName]))
			return $params;

		$hookParams = array();
		foreach($params as &$param)
			$hookParams[] = &$param;

		foreach($this->_hooks[$hookName] as $curCallback)
			call_user_func_array($curCallback, $hookParams);

		return $hookParams;
	}

	/*
	 * Associe une fonction $callback à $hookname
	 */
	public function bind($hookName, callable $callback)
	{
		if(empty($this->_hooks[$hookName]))
		{
            $this->_hooks[$hookName] = array($callback);
		}
		else
		{
            $this->_hooks[$hookName][] = $callback;
		}

		return $this;
	}

	/*
	 * Renvoit TRUE si des callbacks sont associés à $hookName
	 */
	public function exists($hookName)
	{
		return !empty($this->_hooks[$hookName]);
	}
}