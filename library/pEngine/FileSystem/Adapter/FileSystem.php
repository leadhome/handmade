<?php
class pEngine_FileSystem_Adapter_FileSystem extends pEngine_FileSystem_Adapter_Abstract
{
	/**
     * Write image
     *
     * @param  array	$options
     */
	public function writeFile($options = array())
	{
		if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['path']	= array_shift($args);
			$options['bytes']	= array_shift($args);
        } else if (!is_array($options)) {
            $options = array('path' => $options);
			$options = array('bytes' => $options);
        }
        
		if (!isset($options['path']))
		{
			throw new Zend_Exception("Path is not set.");
		}

		if (!isset($options['bytes']))
		{
			throw new Zend_Exception("Bytes is not set.");
		}

		if (!$handle = fopen($options['path'], 'w'))
		{
			throw new Zend_Exception("Can not open file ".$options['path']);
			exit;
		}
		else
		{
			if (fwrite($handle, $options['bytes']) === FALSE)
			{
				throw new Zend_Exception("Can not make file writting ".$options['path']);
				exit;
			}
		}
		fclose($handle);
		return $options['path'];
	}

   	public function setFile($file = null)
	{
		if (isset($image))
		{
			$this->_options['file'] = $file;
			if (isset($this->_file))
			{
				$this->_file->clear();
				$this->_file->destroy();
				unset($this->_file);
			}
		}
	}

   	public function getFile()
	{
		return $this->_file;
	}
}
?>
