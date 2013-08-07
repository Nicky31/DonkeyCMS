 <?php
/*
 * Classe mère de tous les controllers
 */

abstract class Controller extends ModuleComponent
{
    // Instance du loader
    protected $_loader = NULL;
    
    public function __construct($moduleName)
    {
        parent::__construct($moduleName);
        $this->_loader = Loader::instance();
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
        if($path = Finder::helperPath($name, $this->_moduleName))
        {
            $this->_loader->getFile($path);
        }
        else
        {
            throw new DkException('helper.inexistant', $name);
        }
    }
    
    public function library($name, $params = array())
    {
        if($this->_loader->classExists($name))
        {
            return new $name($params);
        }
        else
        {
            throw new DkException('library.inexistant', $name);
        }
    }
    
    public function model($modelName,$dbName = '')
    {
        $modelName = $modelName . MODELSUFFIX;

        $model = new $modelName($dbName);
        $this->_module->addModel($model);
        return $model;
    }

    // Raccourcit la création de vue
    public function view($name, $params = array(), $var = 'content')
    {
        $view = new OutputContent(Finder::viewPath($name, $this->_moduleName, $this->getTheme()), $params);
        $this->addView($view, $var);
            
        return $view;
    }

    public function addView()
    {
        call_user_func_array(array($this->_module, 'addView'), func_get_args());
    }
}