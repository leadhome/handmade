<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Response
 *
 * @author yura
 */
class pEngine_Sms_Response {

    /**
     * Message
     * @var String
     */
    protected $msg;
    /**
     * SMS
     * @var pEngine_Sms_Response
     */
    protected static $inst=null;
    /**
     * Get Singleton pEngine_Sms_Response object
     * @return pEngine_Sms_Response
     */
    public static function getInstance(){
        if(isset(self::$inst))
            return self::$inst;
        return self::$inst = new self();
    }

    /**
     * Set message
     * @param String $msg
     * @return pEngine_Sms_Response 
     */
    public function setMsg($msg){
        $this->msg = $msg;
        return $this;
    }

    /**
     * Send message in phone
     */
    public function send(){
        try {
            Zend_Controller_Front::getInstance()->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8');
//            echo 'status:reply';
//            echo "\n\n";
            echo $this->msg;

        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
}