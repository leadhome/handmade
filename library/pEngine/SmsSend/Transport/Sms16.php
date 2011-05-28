<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 23.05.11
 * Time: 16:41
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_SmsSend_Transport_Sms16 extends pEngine_SmsSend_Transport_Abstract{

    public function __construct($option=array()){
        if(!isset($option['login'])){
            throw new Exception('not login in smssend transport sms16');
        }

        if(!isset($option['password'])){
            throw new Exception('not password in smssend transport sms16');
        }

        parent::__construct($option);
    }

    public function send($from, $to, $body, $charset)
    {
        $body = htmlspecialchars($body);
        $src = '<?xml version="1.0" encoding="'.$charset.'"  ?>
<request>
   <message>
     <sender>'.$from.'</sender>
     <text>'.$body.'</text>
     <abonent phone="'.$to.'" number_sms="1"/>
   </message>
   <security>
     <login value="'.$this->option['login'].'" />
     <password value="'.$this->option['password'].'" />
   </security>
</request>';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset='.$charset));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSLVERSION,3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $src);
        curl_setopt($ch, CURLOPT_URL, 'https://my.sms16.ru/xml/');
        $result = curl_exec($ch);
        curl_close($ch);
        $xml = simplexml_load_string($result);
        $message = (string) $xml->information[0];
        if($message!='send'){
            throw new Exception($message);
        }
        return true;
    }
}