<?php

/**
 * @author Dmitriy Burlutskiy
 */

require_once dirname(__FILE__).'/../Autoloader.php';
pEngine_Autoloader::register();

class pEngine_Tool_pEngineProvider
    extends Zend_Tool_Framework_Provider_Abstract
    implements Zend_Tool_Framework_Provider_Interface
{

    const SUCCESS = 0;
    const WARNING = 1;
    const FATAL   = 2;

    public function getName()
    {
        return 'pEngine';
    }

    public function version(){
        try {
            if (!class_exists('pEngine_Version'))
                throw new Exception ('pEngine not installed! Class pEngine_Version is not found'."\n".' in '. get_include_path(), self::FATAL);
            $v = pEngine_Version::getVersion();
            $this->_registry->getResponse()->appendContent('pEngine version: '.$v, array('color' => 'green'));
        }catch (Exception $e){
            $this->_registry->getResponse()->appendContent($e->getMessage(), array('color' => 'red'));
            exit($e->getCode());
        }
    }
}