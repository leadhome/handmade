<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 23.05.11
 * Time: 16:20
 * To change this template use File | Settings | File Templates.
 */
class pEngine_SmsSend{

    protected static $defaultFrom=null;

    /**
     * @var pEngine_SmsSend_Transport_Abstract
     */
    protected static $defaultTransport=null;

    protected static $defaultCharset = 'utf-8';

    protected $from=null;

    /**
     * @var pEngine_SmsSend_Transport_Abstract
     */
    protected $transport=null;

    protected $charset = null;

    protected $to;

    protected $body='';

    public static function setDefaultFrom($from){
        self::$defaultFrom = $from;
    }

    /**
     * @static
     * @param pEngine_SmsSend_Transport_Abstract $transport
     * @return void
     */
    public static function setDefaultTransport(pEngine_SmsSend_Transport_Abstract $transport){
        self::$defaultTransport = $transport;
    }

    public static function setDefaultCharset($charset){
        self::$defaultCharset = $charset;
    }

    public function setFrom($from){
        $this->from = $from;
    }

    /**
     * @param pEngine_SmsSend_Transport_Abstract $transport
     * @return void
     */
    public function setTransport(pEngine_SmsSend_Transport_Abstract $transport){
        $this->transport = $transport;
    }

    public function setCharset($charset){
        $this->charset = $charset;
    }


    public function __construct($charset = null){
        if(isset($charset)){
            $this->charset = $charset;
        }
    }

    public function setTo($to){
        $this->to = $to;
        return $this;
    }

    public function setBody($text){
        $this->body = $text;
    }

    /**
     * @throws Exception
     * @return bool
     */
    public function send(){
        $transport = $this->getTransport();
        if(!isset($this->to))
            throw new Exception('Not sms send to');
        $to = $this->to;
        $from = $this->getFrom();
        $body = $this->body;
        $charset = $this->getCharset();

        return $transport->send($from,$to,$body,$charset);
    }

    public function getFrom(){
        if(isset($this->from))
            return $this->from;
        if(isset(self::$defaultFrom))
            return self::$defaultFrom;

        throw new Exception('Not sms send from');
    }

    public function getCharset(){
        if(isset($this->charset))
            return $this->charset;
        if(isset(self::$defaultCharset))
            return self::$defaultCharset;

        throw new Exception('Not sms send Charset');
    }

    /**
     * @throws Exception
     * @return null|pEngine_SmsSend_Transport_Abstract
     */
    public function getTransport(){
        if(isset($this->transport))
            return $this->transport;
        if(isset(self::$defaultTransport))
            return self::$defaultTransport;

        throw new Exception('Not sms send transport');
    }
}
