<?php
/**
 * ImageMagic adapter for pEngine_Image
 * @package pEngine
 * @subpackage pEngine_Image_Adapter
 */
class pEngine_Image_Adapter_ImageMagic extends pEngine_Image_Adapter_Abstract
{
	/**
     * Adapter names constants
     */
    const RTYPE_WIDTH			= 'width';
    const RTYPE_HEIGHT 			= 'height';
    const RTYPE_SMART 			= 'smart';
    const RTYPE_SMARTER         = 'smarter';
	/**
     * Resize image
     *
     * @param  array	$options (optional)
     */
   	public function resizeImage($options = array())
	{
		if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['type'] = array_shift($args);
            
			if ($options['type'] == $this::RTYPE_HEIGHT) {		        
		        if (!empty($args)) {
		            $options['height'] = array_shift($args);
		        }
		        
				if (!empty($args)) {
		            $options['width'] = array_shift($args);
		        }
			} else {
             	if (!empty($args)) {
		            $options['width'] = array_shift($args);
		        }
		        
		        if (!empty($args)) {
		            $options['height'] = array_shift($args);
		        }
			} 
        } 
        
		if (!isset($this->_image)) {
			throw new Zend_Exception("Image is not set yet.");
		}
		if (!isset($options['type'])) {
			$options['type'] = $this::RTYPE_SMART;
		}
		if (!isset($options['width']) and !isset($options['height']))
		{
			throw new Zend_Exception("Width and height is not set.");
		}
		if (isset($options['width'])) {
			if($options['width'] > $this->_options['maxWidth']) {
				throw new Zend_Exception($options['width'] . " is greater than maxWidth (" . $this->_options['maxWidth'] . ").");
			}
			if($options['width'] < $this->_options['minWidth']) {
				throw new Zend_Exception($options['width'] . " is less than minWidth (" . $this->_options['minWidth'] . ").");
			}
		}
		if (isset($options['height'])) {
			if($options['height'] > $this->_options['maxHeight']) {
				throw new Zend_Exception($options['height'] . " is greater than maxHeight (" . $this->_options['maxHeight'] . ").");
			}
			if($options['height'] < $this->_options['minHeight']) {
				throw new Zend_Exception($options['height'] . " is less than minHeight (" . $this->_options['minHeight'] . ").");
			}
		}
		
		$method = 'resizeImage' . ucfirst($options['type']);
		
		if (method_exists($this, $method)) {
			$this->$method($options);
        }
        else {
        	throw new Zend_Exception("Unknown type of resize ( " . $options['type'] . ")");
        }
	}
	
	/**
     * Crop image
     *
     * @param  array	$options (optional)
     */
   	public function cropImage($options = array())
	{
		if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['width'] = array_shift($args);
            
            if (!empty($args)) {
                $options['height'] = array_shift($args);
            }
            
            if (!empty($args)) {
                $options['x'] = array_shift($args);
            }
            
            if (!empty($args)) {
                $options['y'] = array_shift($args);
            }
        }
        
		if (!isset($this->_image)) {
			throw new Zend_Exception("Image is not set yet.");
		}
		if (!isset($options['width']) or !isset($options['height']) or !isset($options['x']) or !isset($options['y']))
		{
			throw new Zend_Exception("Params is not set.");
		}
		
		$this->_image->cropImage($options['width'], $options['height'], $options['x'], $options['y']);
	}
	
	/**
     * Save image 
     *
     * @param  array	$options
     */
	public function saveImage($options = array())
	{
		if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['path'] = array_shift($args);
            
            if (!empty($args)) {
                $options['quality'] = array_shift($args);
            }
        } elseif (!is_array($options)) {
            $options = array('path' => $options);
        }
        
		if (!isset($options['path'])) {
			throw new Zend_Exception("Path is not set.");
		}
		
		$date = new Zend_Date();
		//$options['path'] = (strrchr($options['path'], ".") ? preg_replace('/\..*/', '', $options['path']) : $options['path']) . '.' . strtolower($this->_image->getImageFormat());
		if (isset($options['quality'])) {
			$this->setOptions(array('quality' => $options['quality']));
		}
		
		$this->_image->stripImage();		
		$this->_image->setImageCompressionQuality($this->_options['quality']);
                //print_r($options['path']);die();
		$this->_image->writeImages($options['path'], true);
		return $options['path'];
	}
	
	/**
     * Set image object
     *
     * @param  string	$image
     */
   	public function setImage($image = null)
	{
		if (isset($image)) {
			$this->_options['image'] = $image;
			
			if (isset($this->_image)) {
				$this->_image->clear();
				$this->_image->destroy();
				unset($this->_image);
			}
		}
		
		if (filesize($this->_image) > $this->_options['maxSize']*1024*1024) {
			throw new Zend_Exception("Size of file is greater than maxSize (" . $this->_options['maxSize'] . ").");
		}
		
		try {
			$this->_image = new Imagick($this->_options['image']);
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}
	
	/**
     * Return image object
     *
     * @param  string	$image
     */
   	public function getImage()
	{
		return $this->_image;
	}
	
	/**
     * Resize image by width
     *
     * @param  array	$options
     */
	private function resizeImageWidth($options = array())
	{
		if (!isset($options['width'])) {
			throw new Zend_Exception("Width is not set.");
		}
		if ($this->_image->getImageFormat() == 'GIF') {
			//$this->_image->setFirstIterator();
			$this->_image = $this->_image->coalesceImages();
			do {
				$this->_image->thumbnailImage($options['width'], 0);
			} while ($this->_image->nextImage());
			//$this->_image->setFirstIterator();
			$this->_image = $this->_image->deconstructImages();
		} else {		
			$this->_image->thumbnailImage($options['width'], 0);
		}
	}
	
	/**
     * Resize image by height
     *
     * @param  array	$options
     */
	private function resizeImageHeight($options = array())
	{
		if (!isset($options['height'])) {
			throw new Zend_Exception("Height is not set.");
		}
		if ($this->_image->getImageFormat() == 'GIF') {
			//$this->_image->setFirstIterator();
			$this->_image = $this->_image->coalesceImages();
			do {
				$this->_image->thumbnailImage(0, $options['height']);
			} while ($this->_image->nextImage());
			//$this->_image->setFirstIterator();
			$this->_image = $this->_image->deconstructImages();
		} else {
			$this->_image->thumbnailImage(0, $options['height']);
		}
	}
	
	/**
     * Smart resize image
     *
     * @param  array	$options
     */
	private function resizeImageSmart($options = array())
	{
		if (!isset($options['height'])) {
			throw new Zend_Exception("Height is not set.");
		}
		
		$old_width = $this->_image->getImageWidth();
		$old_height = $this->_image->getImageHeight();
		$proc = $old_height / $options['height'];
		
		if (($old_width/$proc) > $options['width']) {
			$new_width = $options['width'];
			$new_height = $old_height/$proc;
			$x = ($old_width/$proc - $options['width'])/2;
			$y = 0;
			$type = 'height';
		} else {
			$proc = $old_width/$options['width'];
			$new_width = $old_width/$proc;
			$new_height = $options['height'];
			$x = 0;
			$y = (($old_height/$proc - $options['height'])/3);
			$type = 'width';			
		}	
		
		if ($this->_image->getImageFormat() == 'GIF') {
			//$this->_image->setFirstIterator();
			$this->_image = $this->_image->coalesceImages();
			do {
				if ($type == 'width') {
					$this->_image->thumbnailImage($options['width'], 0);
				} else {
					$this->_image->thumbnailImage(0, $options['height']);
				}
				$this->_image->cropImage($new_width, $new_height, $x, $y);
			} while ($this->_image->nextImage());
			//$this->_image->setFirstIterator();
			$this->_image = $this->_image->deconstructImages();
		} else {
			$this->_image->thumbnailImage(0, $options['height']);
		}		
	}

    /**
     * Smarter resize image - fit image to given width and height
     *
     * @param  array	$options
     */
    private function resizeImageSmarter($options = array())
	{
        fb_log($options['width'], $options['height']);
		if (!isset($options['height'])) {
			throw new Zend_Exception("Height is not set.");
		}

		$old_width = $this->_image->getImageWidth();
		$old_height = $this->_image->getImageHeight();

        if ($old_width <= $options['width'] && $old_height <= $options['height'])
            return;

		$f = $options['height'] / $old_height;
        fb_log('$f', $old_height, $options['height'], $f);

        fb_log('$f * $old_width', $f * $old_width);
        fb_log('$options[width]', $options['width']);

        if (($f * $old_width) > $options['width']) {
            fb_log('resize case 1');
            $f = $options['width'] / $old_width;
			$new_width = $options['width'];
            $new_height = $f * $old_height;
		} else {
            fb_log('resize case 2');
            fb_log('$f * $old_width', $f, $old_width, $f * $old_width);
            $new_width = $f * $old_width;
            $new_height = $options['height'];
		}

    	$this->_image->resizeImage($new_width, $new_height, imagick::FILTER_SINC, 1.0);
	}
}
?>
