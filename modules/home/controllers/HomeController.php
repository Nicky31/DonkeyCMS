<?php

class HomeController extends Controller
{
    public function index()
    {
        $this->helper('urlManager');
        $this->output()->view('test.php', array('msg' => 'Home Controller'));
    }
}