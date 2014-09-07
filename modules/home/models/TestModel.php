<?php
namespace HomeModule;

class TestModel extends \Model
{
    public function test()
    {
        $query = $this->_db -> query('SELECT * FROM challenge');
        return $query->fetchAll();
    }
}