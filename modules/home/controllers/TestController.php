<?php

class TestController extends Controller
{
    public function index()
    {
        $this->helper('urlManager');
        
        $this->view('test2.php', array('msg' => 'TestController'));
    }
}