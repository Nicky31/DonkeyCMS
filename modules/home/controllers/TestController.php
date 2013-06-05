<?php

class TestController extends Controller
{
    public function index()
    {
        $this->helper('urlManager');
        $this->output()->view('test2.php', array('msg' => 'Test Controller'));
    }
}