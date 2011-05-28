<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 20.04.11
 * Time: 13:20
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Acl_Rule_Ini_Path_Zend extends pEngine_Acl_Rule_Ini_Path_Abstract{

    
    public function getIniPath()
    {
        if(!isset($this->params['module'])){
            $this->params['module'] = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
        }
        
        if(!isset($this->params['controller'])){
            $this->params['controller'] = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        }

        $ds = DIRECTORY_SEPARATOR;
        $file = APPLICATION_PATH.$ds.'modules'.$ds.$this->params['module'].$ds.'acl'.$ds.$this->params['controller'].'.ini';
        return is_readable($file) ? $file : null;

    }


    public function setParam($value = null)
    {
        switch(count($this->params)){
            case 0:
                $this->params['controller']=$value;
            break;
            case 1:
                $this->params['module']=$value;
            break;
        }
        return $value;
    }
}