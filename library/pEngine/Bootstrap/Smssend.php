<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 23.05.11
 * Time: 17:05
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Bootstrap_smssend extends Zend_Application_Resource_ResourceAbstract{

    /**
     * Strategy pattern: initialize resource
     *
     * @return mixed
     */
    public function init()
    {
        $options = $this->getOptions();

        if(isset($options['transport']) AND isset($options['transport']['type'])){
            if(strpos($options['transport']['type'],'_')===false)
                $classTransport = 'pEngine_SmsSend_Transport_'.ucfirst($options['transport']['type']);
            else
                $classTransport = $options['transport']['type'];

            unset($options['transport']['type']);
            
            try{
                $transport = new $classTransport($options['transport']);
            }catch(Exception $e){
                throw $e;
            }
            pEngine_SmsSend::setDefaultTransport($transport);
        }

        if(isset($options['from']))
            pEngine_SmsSend::setDefaultFrom($options['from']);

        if(isset($options['charset']))
            pEngine_SmsSend::setDefaultCharset($options['charset']);
        
    }
}