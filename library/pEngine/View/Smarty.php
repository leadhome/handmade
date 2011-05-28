<?php 
/**
 * pEngine Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   pEngine
 * @package    pEngine_View

 * @version    $Id: Smarty.php 20907 2010-07-04 18:08:01Z matthew $
 */

include_once 'Smarty-2.6.26/libs/Smarty.class.php';

class pEngine_View_Smarty extends Zend_View
{
	
	protected static $smartyOptions=array();
	/*
	 * Директория скомпилированых шаблонов
	 * @var string
	 */
	protected $compile_dir;
	
	protected $config_dir;
	
	protected $cache_dir;
	
	protected $_file;
	
	protected $_filter=array();
	
	protected $chmodDir=0777;
	
	protected $var=array();
	
	protected $objects=array();
	
    /**
     * Объект Smarty
     * @var Smarty
     */
    protected static $_smarty=null;

    public function __construct($config = array())
    {
    	if(self::$_smarty===null)
    	{
    		self::$_smarty=new Smarty();
    	}
    	
    	$this->_setSmartyOptions($config);
    	//var_dump(self::$_smarty);
    	parent::__construct($config);
    }
    
    
    
    protected function _setSmartyOptions($config=array())
    {
    	// compile
        if (array_key_exists('compile_dir', $config))
            self::$smartyOptions['compile_dir']=$config['compile_dir'];
            
    	// config
        if (array_key_exists('config_dir', $config))
            self::$smartyOptions['config_dir']=$config['config_dir'];

    	// config
        if (array_key_exists('cache_dir', $config))
            self::$smartyOptions['cache_dir']=$config['cache_dir'];
            
    	// security
        if (array_key_exists('security', $config))
            self::$_smarty->security=(boolean)$config['security'];            

            
    	// security
        if (array_key_exists('security_settings', $config))
        {
        	$security_settings= &$config['security_settings'];
        	if (array_key_exists('PHP_HANDLING', $security_settings))
            	self::$_smarty->security_settings['PHP_HANDLING']=(boolean)$security_settings['PHP_HANDLING'];   

         	if (array_key_exists('PHP_TAGS', $security_settings))
            	self::$_smarty->security_settings['PHP_TAGS']=(boolean)$security_settings['PHP_TAGS'];             	
            	
        	if (array_key_exists('ALLOW_CONSTANTS', $security_settings))
            	self::$_smarty->security_settings['ALLOW_CONSTANTS']=(boolean)$security_settings['ALLOW_CONSTANTS'];              	
            	//self::$_smarty->$security=$config['security'];
        }
                              
    	// Отладка
        if (array_key_exists('debugging', $config))
            self::$_smarty->debugging=(boolean)$config['debugging']; 
    }
    
    protected function setSmartyVar()
    {
    	//Помошники
        self::$_smarty->assign('helper',new pEngine_View_Helpers($this));
        //Переменые
        foreach($this->var AS $key => $value)
        {
        	self::$_smarty->assign($key,$value);
        }
       
        
        //Объкты
        foreach($this->objects AS $name => $object)
        {
        	self::$_smarty->register_object($name, $object['object_impl'], $object['allowed'], $object['smarty_args'], $object['block_methods']);
        }
        
        
    }
    
    protected function cleanerSmartyVar()
    {
    	self::$_smarty->clear_all_assign();
    	foreach($this->objects AS $name => $object)
        {
            self::$_smarty->unregister_object($name);
        }
    }
    
    
    public function render($name)
    {

        // find the script file name using the parent private method
        $this->_file = $this->_script($name);
        unset($name); // remove $name from local scope
        
        $pathToAplication = realpath(APPLICATION_PATH);
        
        $file = str_replace($pathToAplication,'',$this->_file);
        
        $pathArray = explode(DIRECTORY_SEPARATOR,$file);
        array_shift($pathArray);
        
        $filename = array_pop($pathArray);
        $dir = implode(DIRECTORY_SEPARATOR,$pathArray);
        
        $this->_createCompileDir($dir);
        
        //Устанавливаем переменые окружения Smarty
        $this->setSmartyVar();
        
        //Устанавливаем директорий
        self::$_smarty->compile_dir=self::$smartyOptions['compile_dir'].$dir.DIRECTORY_SEPARATOR;
        self::$_smarty->template_dir=$pathToAplication.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR;
        self::$_smarty->config_dir=self::$smartyOptions['config_dir'];
        self::$_smarty->cache_dir=self::$smartyOptions['cache_dir'];  
		
        //Редеринг
        $render = self::$_smarty->fetch($filename);
        
        //Очищаем переменые
        $this->cleanerSmartyVar();
        
        //пропускаем через фильтры
        return $this->_filter($render);
    }

    /**
     * Infiltrate content
     * @param string $buffer after render content
     * @return string
     */

    protected function _filter($buffer)
    {
        // loop through each filter class
        foreach ($this->getFilters() as $name) {
            // load and apply the filter class
            $filter = $this->getFilter($name);
            $buffer = call_user_func(array($filter, 'filter'), $buffer);
        }
        // done!
        return $buffer;
    }
    /**
     * Created directory for compile file
     * @param strng $dir if not dir, to created dir
     * @return void
     */
    protected function _createCompileDir($dir)
    {
    	if(is_dir(self::$smartyOptions['compile_dir'].$dir))
    		return;

    	$dirCreate = self::$smartyOptions['compile_dir'];
    	$dirNew = explode(DIRECTORY_SEPARATOR,$dir);

    	foreach($dirNew AS $name)
    	{
    		$dirCreate.=$name.DIRECTORY_SEPARATOR;

    		if(is_dir($dirCreate))
    			continue;
    		mkdir($dirCreate,$this->chmodDir);
    	}
    }
    
    /**
     * Assigns variables to the view script via differing strategies.
     *
     * pEngine_View_Smarty::assign('name', $value) assigns a variable called 'name'
     * with the corresponding $value.
     *
     * pEngine_View_Smarty::assign($array) assigns the array keys as variable
     * names (with the corresponding array values).
     *
     * @see    __set()
     * @param  string|array The assignment strategy to use.
     * @param  mixed (Optional) If assigning a named variable, use this
     * as the value.
     * @return pEngine_View_Smarty
     * @throws Zend_View_Exception if $spec is neither a string nor an array,
     * or if an attempt to set a private or protected member is detected
     */
    public function assign($spec, $value = null)
    {
        // which strategy to use?
        if (is_string($spec)) {
            // assign by name and value
            if ('_' == substr($spec, 0, 1)) {
                require_once 'Zend/View/Exception.php';
                $e = new Zend_View_Exception('Setting private or protected class members is not allowed');
                $e->setView($this);
                throw $e;
            }
            $this->var[$spec] = $value;
        } elseif (is_array($spec)) {
            // assign from associative array
            $error = false;
            foreach ($spec as $key => $val) {
                if ('_' == substr($key, 0, 1)) {
                    $error = true;
                    break;
                }
                $this->var[$key] = $val;
            }
            if ($error) {
                require_once 'Zend/View/Exception.php';
                $e = new Zend_View_Exception('Setting private or protected class members is not allowed');
                $e->setView($this);
                throw $e;
            }
        } else {
            require_once 'Zend/View/Exception.php';
            $e = new Zend_View_Exception('assign() expects a string or array, received ' . gettype($spec));
            $e->setView($this);
            throw $e;
        }

        return $this;
    }
    
   /**
     * Registers object to be used in templates
     *
     * @param string $object name of template object
     * @param object &$object_impl the referenced PHP object to register
     * @param null|array $allowed list of allowed methods (empty = all)
     * @param boolean $smarty_args smarty argument format, else traditional
     * @param null|array $block_functs list of methods that are block format
     * @return void
     */
    function register_object($object, $object_impl, $allowed = array(), $smarty_args = true, $block_methods = array())
    {
    	$this->objects[$object]=array(
    		'object_impl'=>$object_impl,
    		'allowed'=>$allowed,
    		'smarty_args'=>$smarty_args,
    		'block_methods'=>$block_methods
    	);
    }
    
    /**
     * Allows testing with empty() and isset() to work inside
     * templates.
     *
     * @param  string $key
     * @return boolean
     */
    public function __isset($key)
    {
        if ('_' != substr($key, 0, 1)) {
            return isset($this->var[$key]);
        }

        return false;
    }

    /**
     * Directly assigns a variable to the view script.
     *
     * Checks first to ensure that the caller is not attempting to set a
     * protected or private member (by checking for a prefixed underscore); if
     * not, the public member is set; otherwise, an exception is raised.
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     * @throws Zend_View_Exception if an attempt to set a private or protected
     * member is detected
     */
    public function __set($key, $val)
    {
        if ('_' != substr($key, 0, 1)) {
            $this->var[$key] = $val;
            return;
        }

        require_once 'Zend/View/Exception.php';
        $e = new Zend_View_Exception('Setting private or protected class members is not allowed');
        $e->setView($this);
        throw $e;
    }

    /**
     * Directly assigns a variable to the view script.
     *
     * Checks first to ensure that the caller is not attempting to set a
     * protected or private member (by checking for a prefixed underscore); if
     * not, the public member is set; otherwise, an exception is raised.
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     * @throws Zend_View_Exception if an attempt to set a private or protected
     * member is detected
     */
    public function __get($key)
    {
        if ('_' != substr($key, 0, 1)) {
            return $this->var[$key];
        }

        require_once 'Zend/View/Exception.php';
        $e = new Zend_View_Exception('Setting private or protected class members is not allowed');
        $e->setView($this);
        throw $e;
    }    
    /**
     * Allows unset() on object properties to work
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        if ('_' != substr($key, 0, 1) && isset($this->$key)) {
            unset($this->var[$key]);
        }
    }
}
