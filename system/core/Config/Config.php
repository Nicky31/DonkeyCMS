<?php

/*
 * Classe gérant la gestion d'une config
 * Instanciée par ConfigMgr
 */

class Config implements ArrayAccess
{
    // Contenu de la configuration dans un tableau :
    private $_content = array();
    // Nom de la config utilisé en interne
    private $_name    = NULL;
    
    public function __construct($params)
    {
        $this->_content = Loader::instance()->getFile($params['path'], TRUE);
        $this->_name = $params['name'];
    }
    
    public function getItem($k)
    {
        if($this->itemExists($k))
            return $this->_content[$k];
        
        throw new Exception('<b>'. __CLASS__ . '</b> : L\'item <b>'. $k .'</b> n\'existe pas dans la configuration <b>'. $this->_name .'</b> !');
    }
    
    public function rewriteItem($k,$v)
    {
        $this->_content[$k] = $v;
    }
    
    public function itemExists($k)
    {
        return isset($this->_content[$k]);
    }
    
    public function unsetItem($k)
    {
        if($this->itemExists($k))
        {
            unset($this->_content[k]);
            return TRUE;
        }
        return FALSE;
    }
    
    public function offsetSet($offset, $value)
    {
        return $this->rewriteItem($offset, $value);
    }
    
    public function offsetGet($offset)
    {
        return $this->getItem($offset);
    }
    
    public function offsetExists($offset)
    {
        return $this->itemExists($offset);
    }
    
    public function offsetUnset($offset)
    {
        return $this->unsetItem($offset);
    }
}