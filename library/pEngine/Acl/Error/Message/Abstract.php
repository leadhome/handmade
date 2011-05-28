<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 25.04.11
 * Time: 15:26
 * To change this template use File | Settings | File Templates.
 */
 
abstract class pEngine_Acl_Error_Message_Abstract extends pEngine_Acl_Error_Abstract{

    protected $message = null;

    protected $code=null;

    public function setParams(array $option=null){
        $this->message = isset($option['message']) ? $option['message'] : 'Not allowed';
        $this->code = isset($option['code']) ? $option['code'] : 403;
        return $this;
    }
}