<?php
    
class HomeController extends Controller
{
    public function __construct($Module)
    {
        parent::__construct($Module);
        $this->helper('urlManager');        
    }
    
    public function index()
    {
        if(($var = Input::get(0, 'GET')) === NULL)
        {
            $var = 'param non renseigné';
        }
        $this->output()->view('test.php', array('var' => $var));
    }
}