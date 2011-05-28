<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 16.05.11
 * Time: 13:25
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Bootstrap_Robokassa extends Zend_Application_Resource_ResourceAbstract{

    protected $serverName;

    protected $pass1;

    protected $pass2;

    protected $login;

    protected $handlers=array();

    /**
     * Strategy pattern: initialize resource
     *
     * @return mixed
     */
    public function init()
    {
        $options = $this->getOptions();
        // Server name

        $this->serverName = (!isset($options['status']) OR $options['status']!='test') ?
                'https://merchant.roboxchange.com' :
                'http://test.robokassa.ru';
        
        $this->pass1 = $options['pass1'];
        $this->pass2 = $options['pass2'];
        $this->login = $options['login'];

        return $this;
    }
    /**
     * @static
     * @return pEngine_Bootstrap_Robokassa
     */
    public static function getInstance(){
        return Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('robokassa');
    }

    public function getLink($id,$price,$description='',$type=null){
        $params = array(
            'MrchLogin'=>$this->login,
            'OutSum'=>$price,
            'InvId'=>$id,
            'Desc'=>$description,
            'SignatureValue'=>md5($this->login.':'.$price.':'.$id.':'.$this->pass1),
            //'IncCurrLabel'=>$this->login,
            'Culture'=>'ru',
        );

        if(isset($type)){
            $params['IncCurrLabel']=$type;
        }
        
        $paramsUrl = array();
        foreach($params AS $key=>$value){
            $paramsUrl[]=$key.'='.$value;
        }

        $url = $this->serverName.'/Index.aspx?'.implode('&',$paramsUrl);
        return $url;
    }



    public function result(){
        $logger = Zend_Registry::getInstance()->get('logger');
        $id = $_REQUEST['InvId'];
        $price = $_REQUEST['OutSum'];

        if(strtoupper(md5($price.':'.$id.':'.$this->pass2))!=strtoupper($_REQUEST['SignatureValue'])){
            $logger->info(json_encode($_REQUEST));
            exit('r Not SignatureValue');
        }
        call_user_func($this->getHandler('result'),$id,$price);

        echo 'OK'.$id;

    }

    public function success(){
        $id = $_REQUEST['InvId'];
        $price = $_REQUEST['OutSum'];
        $logger = Zend_Registry::getInstance()->get('logger');
        if(strtoupper(md5($price.':'.$id.':'.$this->pass1))!=strtoupper($_REQUEST['SignatureValue'])){
            $logger->info(json_encode($_REQUEST));
            exit('s Not SignatureValue');
        }

        call_user_func($this->getHandler('success'),$id,$price);
    }

    public function fail(){
        $id = $_REQUEST['InvId'];
        $price = $_REQUEST['OutSum'];
        $logger = Zend_Registry::getInstance()->get('logger');
        if(strtoupper(md5($price.':'.$id.':'.$this->pass1))!=strtoupper($_REQUEST['SignatureValue'])){
            $logger->info(json_encode($_REQUEST));
            exit('f Not SignatureValue');
        }

        call_user_func($this->getHandler('fail'),$id,$price);
    }


    /**
     * 0 - Сервер не доступен
     * 5 - только инициирована, деньги не получены
     * 10 - деньги не были получены, операция отменена
     * 50 - деньги от пользователя получены, производится зачисление денег на счет магазина
     * 60 - деньги после получения были возвращены пользователю
     * 80 - исполнение операции приостановлено
     * 100 - операция завершена успешно
     * @param  $id Счёт
     * @return int
     */
    public function check($id){
        $params = array(
            'MerchantLogin'=>$this->login,
            'InvoiceID'=>$id,
            'Signature'=>md5($this->login.':'.$id.':'.$this->pass2),
            'StateCode'=>100,
        );

        $paramsUrl = array();
        foreach($params AS $key=>$value){
            $paramsUrl[]=$key.'='.$value;
        }

        $url = $this->serverName.'/Webservice/Service.asmx/OpState?'.implode('&',$paramsUrl);
        try{
            $xml = file_get_contents($url);
            $xml = simplexml_load_string($xml);
            $status = (string) $xml->State[0]->Code[0];
            $status = intval($status);
        }catch(Exception $e){
            $status = 0;
        }

        return $status;
    }

    protected function setHandler($name,$handler){
        $this->handlers[$name]=$handler;
        return $this;
    }

    protected function getHandler($name){
        return $this->handlers[$name];
    }
    /**
     * @param  $handler
     * @return pEngine_Bootstrap_Robokassa
     */
    public function setResultHandler($handler){
        $this->setHandler('result',$handler);
        return $this;
    }
    /**
     * @param  $handler
     * @return pEngine_Bootstrap_Robokassa
     */
    public function setSuccessHandler($handler){
        $this->setHandler('success',$handler);
        return $this;
    }
    /**
     * @param  $handler
     * @return pEngine_Bootstrap_Robokassa
     */
    public function setFailHandler($handler){
        $this->setHandler('fail',$handler);
        return $this;
    }

    protected function handler($name){
        return call_user_func($this->getHandler($name));
    }
}