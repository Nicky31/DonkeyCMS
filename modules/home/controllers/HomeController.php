<?php
    
class HomeController extends Controller
{
    public function __construct($moduleName)
    {
        parent::__construct($moduleName);
        $this->helper('urlManager');
        //$model = $this->model('Test', 'dbStatic');       
    }
    
    public function index()
    {
        $cache = new Cache('hometest');
        if($cache->isNew())
        {
            if(($var = Input::get(0, 'GET')) === NULL)
            {
                $var = 'param non renseigné';
            }

            $cache -> save(30,
                $this->view('test.php', array('var' => $var))
                    -> at(OutputContent::POS_START)
                    -> insertContent('<b>Ajout texte manuellement au debut de la vue</b>')
            );
        }
        else
        {
            $this->addView($cache->getDatas());
        }
    }

}