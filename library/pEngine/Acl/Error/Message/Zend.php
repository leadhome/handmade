<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 25.04.11
 * Time: 15:32
 * To change this template use File | Settings | File Templates.
 */

class pEngine_Acl_Error_Message_Zend extends pEngine_Acl_Error_Message_Abstract{
    /**
     * Start error
     * @return void
     */
    public function perform()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $errorHandler = false;
        foreach(Zend_Controller_Front::getInstance()->getPlugins() AS $key=>$value){
            if(get_class($value)=='Zend_Controller_Plugin_ErrorHandler'){
                $errorHandler = true;
                Zend_Controller_Front::getInstance()->getResponse()->setException(new Exception($this->message,$this->code));
                $value->postDispatch($request);
                break;
            }
        }

        if(!$errorHandler){
            $request->setModuleName('index')
                    ->setControllerName('error')
                    ->setActionName('error')
                    ->setDispatched(false);
        }
    }
}