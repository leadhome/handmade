<?php
abstract class pEngine_FileSystem_Adapter_Abstract
{	
	/**
	* Array with all options, each adapter can have own additional options
	*   'file'		=> file
	*	'string'	=> string
	*	'method'	=> method writting
	*
	* @var array()
	*/
    public $_options = array(
        'file'		=> null,
		'bytes'		=> null,
    );
    /*
    * File object
    *
	* @var object $_file
	*/
    public $_file = null;
    
	/**
	* Generates the adapter
	*
	* @param  array|Zend_Config $options File options for this adapter
	* @throws Zend_Exception
	* @return void
	*/
    public function __construct($options = array()) 
    {
		if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['file'] = array_shift($args);
        } else if (!is_array($options)) {
            $options = array('file' => $options);
        }
        
        $this->setOptions($options);
        
        if(isset($options['file'])) {
        	$this->setFile();
        }
	}
    
    /**
     * Sets new adapter options
     *
     * @param  array $options Adapter options
     * @throws Zend_Exception
     * @return pEngine_FileSystem_Adapter Provides fluent interface
     */
    public function setOptions(array $options = array())
    {
        foreach ($options as $key => $option) {
            if ((isset($this->_options[$key]) and ($this->_options[$key] != $option)) or !isset($this->_options[$key])) {
                $this->_options[$key] = $option;
            }
        }
        return $this;
    }
    
    /**
     * Returns the adapters name and it's options
     *
     * @param  string|null $optionKey String returns this option
     *                                null returns all options
     * @return integer|string|array|null
     */
    public function getOptions($optionKey = null)
    {
        if ($optionKey === null) {
            return $this->_options;
        }

        if (isset($this->_options[$optionKey]) === true) {
            return $this->_options[$optionKey];
        }

        return null;
    }
    
    /**
     * Write file
     *
     * @param  array	$options
     */
	abstract public function writeFile($options = array());
    
	/**
     * Set file object
     *
     * @param  string	$file
     */
    abstract public function setFile($file);
    
    
	/**
     * Return file object
     *
     * @param  string	$file
     */
    abstract public function getFile();
}
?>
