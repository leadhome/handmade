<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pEngine_Sms_Gateway_Abstract
 *
 * @author yura
 */
abstract class pEngine_Sms_Gateway_Abstract {
    /**
     * Message
     * @var String
     */
    protected $msg;

    /**
     * Params for this gateway
     * @var Array
     */
    protected $params;

    /**
     * Request
     * @var Zend_Controller_Request_Abstract
     */
    protected $request;

    public function  __construct() {
        $this->request = Zend_Controller_Front::getInstance()->getRequest();
    }
    /**
     * Set message came from user
     * @param String $msg
     * @return pEngine_Sms_Gateway_Abstract 
     */
    public function setMsg($msg){
        $this->msg=$msg;
        return $this;
    }

    /**
     * Set Params for this gateway
     * @param Array $params
     * @return pEngine_Sms_Gateway_Abstract
     */
    public function setParams($params){
        $this->params = $params;
        return $this;
    }

    /**
     * processing of the data came
     * @return pEngine_Sms_Gateway_Abstract
     */
    abstract public function process();

    abstract public function mtStatus();

}