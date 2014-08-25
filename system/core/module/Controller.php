 <?php
/*
 * Classe mère de tous les controllers
 */

abstract class Controller
{
    // Suffixe utilisé par tous les controllers
    const     SUFFIX         = 'Controller';
    // Action par défaut si non renseignée par l'url ou invalide
    const     DEFAULT_ACTION = 'index'; 
    // Objet module auquel le controller appartient
    protected $_module       = NULL;

    public function __construct($module)
    {
        $this->_module = $module;
    }
    
    public function helper($name)
    {
        if(is_array($name))
        {
            foreach ($name as $curName)
                $this->helper ($curName);
            return;
        }
        $name = appendExt($name);
        if($path = Finder::helperPath($name, $this->_module->name()))
        {
            Loader::instance()->getFile($path);
        }
        else
        {
            throw new DkException('helper.inexistant', $name);
        }
    }
    
    public function library($name, $params = array())
    {
        if(Loader::instance()->classExists($name))
        {
            return new $name($params);
        }
        else
        {
            throw new DkException('library.inexistant', $name);
        }
    }
    
    public function model($modelName, $dbName = '')
    {
        $modelName = $modelName . Model::SUFFIX;

        $model = new $modelName($dbName);
        $this->_module->addModel($model);
        return $model;
    }

    // Raccourcit la création de vue
    public function view($name, $params = array(), $var = 'content')
    {
        $view = new OutputContent(Finder::viewPath($name, $this->_module->name(), $this->_module->getTheme()), $params);
        $this->addView($view, $var);
            
        return $view;
    }

    public function addView()
    {
        call_user_func_array(array($this->_module, 'addView'), func_get_args());
    }

    // Actions devant être communes à tous les controllers : changement thème/langue
    public function selectTheme()
    {
        call_user_func_array(array($this, DEFAULTACTION), array());
        echo redirect();

        if(!$theme = Input::get(0, 'GET', array( 'pattern'   => '#^[a-z0-9_-]{1,16}$#i' )))
        {
            return;
        }
        if(!is_dir(MODS_PATH . $this->_module->name() .'/themes/' . $theme))
        {
            return;
        }
        
        setcookie(DATASDONKEY .'[modules]['. $this->_module->name() .'][defaultTheme]', $theme, time() + 365*24*3600, '/', null, false, true);
    }
    
    public function selectLang()
    {
        call_user_func_array(array($this, DEFAULTACTION), array());
        echo redirect(); 

        if(!$lang = Input::get(0, 'GET', array( 'pattern'   => '#^[a-z]{2,3}$#i' )))
        {
            return;
        }
        
        setcookie(DATASDONKEY .'[defaultLang]', $lang, time() + 365*24*3600, '/', null, false, true);
    }
}