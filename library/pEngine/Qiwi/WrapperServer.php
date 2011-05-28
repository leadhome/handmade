<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 17.05.11
 * Time: 14:31
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Qiwi_WrapperServer{
    /**
     * @var pEngine_Bootstrap_Qiwi
     */
    protected $resource = null;

    protected $handler = null;

    protected function __construct(){
        $this->resource = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('qiwi');
    }
    
    public static function factory(){
        return new self();
    }

    public function check($handler){
        $this->handler = $handler;
        $options = $this->resource->getOptions();

        $s = new SoapServer($options['wsdl']['client']['path'], array('classmap' => array('tns:updateBill' => 'Param', 'tns:updateBillResponse' => 'Response')));
        $s->setObject($this);
        $s->handle();
    }
    /**
     * @param  Param $params
     * @return Response
     */
    public function updateBill($params){
    	$logger = Zend_Registry::getInstance()->get('logger');
    	$logger->info('Qiwi server: '.json_encode($params));
        $options = $this->resource->getOptions();
        $result = new Response();
        //$result->updateBillResult = -1;
        $password = strtoupper(md5($params->txn.strtoupper(md5($options['password']))));
        if($options['login']==$params->login AND $password==$params->password){
        	$logger->info('Qiwi server: true handler ');
            $result->updateBillResult = call_user_func($this->handler,$params->txn,$params->status);
        }else{
        	$logger->info('Qiwi server: no password '.$password);
            $result->updateBillResult=150;
        }
        return $result;
    }
}

class Response {
 public $updateBillResult;
}

class Param {
 public $login;
 public $password;
 public $txn;
 public $status;
}
