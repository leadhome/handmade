<?php
/**
 * Abstract adapter class for each image adapter
 * @package pEngine
 * @subpackage pEngine_Image_Adapter
 */
abstract class pEngine_Image_Adapter_Abstract
{	
	/**
	* Array with all options, each adapter can have own additional options
	*   'image'			=> image file
	*	'quality'		=> quality of image
	*	'maxSize'		=> max size of image file (Mb)
	*   'minHeight'		=> min height of image
	*   'minWidth'		=> min width of image
	*   'maxHeight'		=> max height of image
	*   'maxWidth'		=> max height of image
	*
	* @var array()
	*/
    public $_options = array(
        'image'			=> null,
        'quality'		=> 100,
        'maxSize'		=> 10,
        'minHeight'		=> 10,
        'minWidth'		=> 10,
        'maxHeight'		=> 2048,
        'maxWidth'		=> 2048,
    );
    /*
    * Image object
    *
	* @var object $_image
	*/
    public $_image = null;
    
	/**
	* Generates the adapter
	*
	* @param  array|Zend_Config $options Image options for this adapter
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
            $options['image'] = array_shift($args);            
        } else if (!is_array($options)) {
            $options = array('image' => $options);
        }
        
        $this->setOptions($options);
        
        if(isset($options['image'])) {
        	$this->setImage();
        }
	}
    
    /**
     * Sets new adapter options
     *
     * @param  array $options Adapter options
     * @throws Zend_Exception
     * @return pEngine_Image_Adapter Provides fluent interface
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
     * Resize image
     *
     * @param  array	$options
     */
    abstract public function resizeImage($options = array());
    
	/**
     * Crop image
     *
     * @param  array	$options
     */
    abstract public function cropImage($options = array());
    
    /**
     * Save image 
     *
     * @param  array	$options
     */
	abstract public function saveImage($options = array());
    
	/**
     * Set image object
     *
     * @param  string	$image
     */
    abstract public function setImage($image);
    
    
	/**
     * Return image object
     *
     * @param  string	$image
     */
    abstract public function getImage();
}
?>
