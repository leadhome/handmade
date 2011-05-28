<?php
 class pEngine_FileSystem_FileSystem
{
	const ADAPTER_FILE_SYSTEM = 'FyleSystem';
	
	protected $_adapter = null;

	public function __construct($options = array())
	{
		if ($options instanceof Zend_Config)
		{
            $options = $options->toArray();
        } 
		else if (func_num_args() > 1)
		{
            $args = func_get_args();
            $options = array();
            $options['adapter'] = array_shift($args);
            if (!empty($args))
			{
                $options['file'] = array_shift($args);
            }
        } 
		else if (!is_array($options))
		{
            $options = array('adapter' => $options);
        }
        $this->setAdapter($options);
	}

	public function setAdapter($options = array())
	{
        if (!isset($options['adapter']))
		{
			$options['adapter'] = $this::ADAPTER_FILE_SYSTEM;
		}

        if (Zend_Loader::isReadable('pEngine/FileSystem/Adapter/' . ucfirst($options['adapter']). '.php'))
		{
            $class_name = 'pEngine_FileSystem_Adapter_' . ucfirst($options['adapter']);
        }
	    else
		{
	    	throw new Zend_Exception("File not found pEngine/FileSystem/Adapter/" . ucfirst($options['adapter']). ".php");
		}

        if (!class_exists($class_name))
		{
            Zend_Loader::loadClass($class_name);
        }

		$this->_adapter = new $class_name($options);

        if (!$this->_adapter instanceof pEngine_FileSystem_Adapter_Abstract)
		{
            throw new Zend_Exception("Adapter " . $class_name . " does not extend pEngine_FileSystem_Adapter_Abstract");
        }
    }

    public function getAdapter()
    {
        return $this->_adapter;
    }

    public function __call($method, array $options)
    {
        if (method_exists($this->_adapter, $method))
		{
            return call_user_func_array(array($this->_adapter, $method), $options);
        }
        else
		{
        	throw new Zend_Exception("Unknown method '" . $method . "' called!");
        }
    }
}
?>
