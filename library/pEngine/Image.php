<?php
/**
 * Class for operations with images
 * @package pEngine
 * @subpackage pEngine_Image
 */
 class pEngine_Image
{
	/**
     * Adapter names constants
     */
    const ADAPTER_IMAGIC		= 'ImageMagic';
    //const ADAPTER_GD 			= 'GD';
	/**
     * Adapter for class Image
     *
     * @var object
     */
	protected $_adapter = null;
	/**
     * Generates the standard Image object
     *
     * @param  array|Zend_Config $options Options to use
     */
	public function __construct($options = array())
	{
		if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['adapter'] = array_shift($args);
            
            if (!empty($args)) {
                $options['image'] = array_shift($args);
            }
        } else if (!is_array($options)) {
            $options = array('adapter' => $options);
        }
        
        $this->setAdapter($options);
	}
	/**
     * Sets a new adapter
     *
     * @param  array|Zend_Config $options Options to use
     */
	public function setAdapter($options = array())
	{
    	if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
           $args               = func_get_args();
            $options            = array();
            $options['adapter'] = array_shift($args);
            
            if (!empty($args)) {
                $options['image'] = array_shift($args);
            }       
        } else if (!is_array($options)) {
            $options = array('adapter' => $options);
        }
        
        if (!isset($options['adapter'])) {
			$options['adapter'] = $this::ADAPTER_IMAGIC;
		}

        if (Zend_Loader::isReadable('pEngine/Image/Adapter/' . ucfirst($options['adapter']). '.php')) {
            $class_name = 'pEngine_Image_Adapter_' . ucfirst($options['adapter']);
        }
	    else {
	    	throw new Zend_Exception("File not found /Image/Adapter/" . ucfirst($options['adapter']). ".php");
		}

        if (!class_exists($class_name)) {
            Zend_Loader::loadClass($class_name);
        }
        
		$this->_adapter = new $class_name($options);
				
        if (!$this->_adapter instanceof pEngine_Image_Adapter_Abstract) {
            throw new Zend_Exception("Adapter " . $class_name . " does not extend pEngine_Image_Adapter_Abstract");
        }
    }
    
    /**
     * Returns the adapters name and it's options
     *
     * @return pEngine_Image_Adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }
    
    /**
     * Magic __call method :^)
     * Calls all methods from the adapter
     */
    public function __call($method, array $options)
    {
        if (method_exists($this->_adapter, $method)) {
            return call_user_func_array(array($this->_adapter, $method), $options);
        }
        else {
        	throw new Zend_Exception("Unknown method '" . $method . "' called!");
        }
    }
}
?>
