<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sms
 *
 * @author yura
 */
class pEngine_Sms {
    /**
     * SMS
     * @var pEngine_Sms
     */
    protected static $inst=null;

    /**
     * Request
     * @var Zend_Controller_Request_Abstract
     */
    protected $request;

    /**
     * Params SMS
     * @var Array
     */
    protected $params;



    private function  __construct() {
        $this->request = Zend_Controller_Front::getInstance()->getRequest();
        $params = Zend_Registry::get('options');
        $this->params = $params['sms'] ;
    }

    public function process(){

        if($this->request->getParam('status')){
            return $this->mtStatus();
        }
        
        if(!$this->isSecret())
            return;
        if($this->params['log']){
            $this->log();
        }
        $info = $this->getGatewayInfo();
        if(!$info)
            return;

        $this->getGateway($info->class)->setMsg($info->msg)->process();
        
        
    }

    protected function isSecret(){
        $key = $this->request->getParam('sign');

        $reference = $this->ref_sign(
                $this->params['key'],
                $this->request->getParam('country'),
                $this->request->getParam('shortcode'),
                $this->request->getParam('provider'),
		$this->request->getParam('prefix'),
                $this->request->getParam('cost_local'),
                $this->request->getParam('cost_usd'),
                $this->request->getParam('phone'),
                $this->request->getParam('msgid'),
                $this->request->getParam('sid'),
                $this->request->getParam('content'));

        if($reference!=$key){
            Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(404);
            return false;
        }
        return true;
    }

    protected function log(){
        $log = Sms_Model_LogTable::getInstance()->getRecord();
        $data = array(
            'smsid'=>$this->request->getParam('msgid'),
            'date'=>date('Y-m-d H:i:s'),
            'operator'=>$this->request->getParam('provider'),
            'country'=>$this->request->getParam('country'),
            'phone'=>$this->request->getParam('phone'),
            'income'=>$this->request->getParam('cost_local'),
            'number'=>$this->request->getParam('shortcode'),
            'msg'=>$this->request->getParam('content'),
            'msg_trans'=>$this->request->getParam('content'),
            'test'=>$this->request->getParam('test',0)
        );

        $log->merge($data);
        $log->save();


    }

    protected function getGatewayInfo(){
        $msg = $this->request->getParam('content');

        foreach($this->params['gateway'] AS $class => $params){
            $name = $params['name'];
            if(preg_match_all('|^'.$this->params['prefix'].'\s*'.$name.'(.*)$|i',$msg,$out)){
                $info = new stdClass();
                $info->name = $name;
                $info->class= $class;
                $info->msg  = $out[1][0];
                return $info;
            }
        }
        $this->errorMsg();
        return false;
    }

    protected function mtStatus(){
        $sms = Sms_Model_LogTable::getInstance()
            ->findOneBy('smsid',$this->request->getParam('msgid', 0));
        switch(strtolower($this->request->getParam('status'))){
            case 'delivered':
                if($sms){
                    $sms->pay_status=1;
                    $sms->save();
                }

            break;
            default:
            if($sms){
                if($this->params['log']){
                    $sms->pay_status=0;
                    $sms->save();
                    $this->request->setParam('content', $sms->msg);
                    $this->request->setParam('shortcode', $sms->number);
                    $info = $this->getGatewayInfo();
                    $this->getGateway($info->class)->setMsg($info->msg)->mtStatus();
                }
            }
            break;
        }
    }

    protected function errorMsg(){
        $msg='Не правильно сформирована смс';
        pEngine_Sms_Response::getInstance()->setMsg($msg)->send();
    }

    /**
     * Create new pEngine_Sms_Gateway
     * @param text $classPrefix
     * @return pEngine_Sms_Gateway_Abstract
     */
    public function getGateway($classPrefix){
        $class = 'pEngine_Sms_Gateway_'.$classPrefix;

        $gateway = new $class();
        $gateway->setParams($this->params['gateway'][$classPrefix]);
        return $gateway;
    }

    protected function ref_sign(){
        $params = func_get_args();
        $prehash = implode("::", $params);
        return md5($prehash);
    }

    /**
     * Get Singleton pEngine_Sms object
     * @return pEngine_Sms
     */
    public static function getInstance(){
        if(isset(self::$inst))
            return self::$inst;
        return self::$inst = new self();
    }


}
