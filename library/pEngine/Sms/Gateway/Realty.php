<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Realty
 *
 * @author yura
 */
class pEngine_Sms_Gateway_Realty extends pEngine_Sms_Gateway_Abstract{

    const ERR_MSG=1;

    const ERR_NUMBER=2;

    const ERR_NOT_REALTY=3;

    const OK=4;
    /**
     * Id Realty
     * @var Integer
     */
    protected $id=null;
    
    /**
     * type do
     * @var String
     */
    protected $type=null;

    /**
     * clock hour
     * @var Integer
     */
    protected $time;
    /**
     * processing of the data came
     * @return pEngine_Sms_Gateway_Abstract
     */
    public function process(){

        if(!$this->renderMsg())
            return $this;

        if(!$this->isIdRealty())
            return $this;

        $this->setData();
        return $this;
    }

    protected function isIdRealty(){
        $realty = Realty_Model_RealtyTable::getInstance()->findOneBy('id',$this->id);
        if(!$realty){
            $this->msg(self::ERR_NOT_REALTY);
            return false;
        }
        return true;
    }

    public function mtStatus(){
        foreach($this->params['type'] AS $type => $value){
            if(preg_match_all('|^'.$value['prefix'].'\s*([0-9]+)$|i',$this->msg,$out)){
                $this->id = $out[1][0];
                $this->type = $type;
            }
        }
        $number = $this->request->getParam('shortcode');
        if(isset($this->params['type'][$this->type]['number'][$number])){
            $this->time = intval($this->params['type'][$this->type]['number'][$number]['time']);
        }

        if(isset($this->id) AND isset($this->type) AND isset($this->time)){
        $sms = Sms_Model_RealtyTable::getInstance()->findOneSms($this->id,$this->type);
            if($sms){
                $time = strtotime($sms->date_cancel);
                $time-=$this->time*60*60;
                $sms->date_cancel=date('Y-m-d H:i:s',$time);
                $sms->save();
            }
        }
    }

    protected function setData(){

        $time = 60*60*$this->time;
        $sms = Sms_Model_RealtyTable::getInstance()->findOneSms($this->id,$this->type);

        if(!$sms){
            $sms = Sms_Model_RealtyTable::getInstance()->getRecord();
            $sms->date_cancel=date('Y-m-d H:i:s',time()+$time);
        }
        else{
            $time_now = strtotime($sms->date_cancel);
            if($time_now<time())
                $time_now=time();
            $sms->date_cancel = date('Y-m-d H:i:s',$time_now+$time);
        }
        $sms->type=$this->type;
        if($this->type=='up'){
            $sms->order_up=1;
        }
        $sms->id_realty=$this->id;
        $sms->save();
        
        $this->msg(self::OK);
    }

    protected function renderMsg(){
        $msg = trim($this->msg);

        foreach($this->params['type'] AS $type => $value){
            if(preg_match_all('|^'.$value['prefix'].'\s*([0-9]+)$|i',$msg,$out)){
                $this->id = $out[1][0];
                $this->type = $type;
            }
        }
        if(!isset($this->id)){
            $this->msg(self::ERR_MSG);
            return false;
        }

        $number = $this->request->getParam('shortcode');

        if(isset($this->params['type'][$this->type]['number'][$number])){
            $this->time = intval($this->params['type'][$this->type]['number'][$number]['time']);
        }else{
            $this->msg(self::ERR_NUMBER);
            return false;
        }

        return true;
        
    }


    protected function msg($do){
        switch($do){
            case self::ERR_MSG:
                $msg='Не правильно составленое смс. Поворите ещё раз.';
                break;
            case self::ERR_NOT_REALTY:
                $msg='Такого номера объявления не существует. Поворите ещё раз.';
                break;
            case self::ERR_NUMBER:
                $msg='Вы отправили смс не на тот номер. Поворите ещё раз.';
                break;
            case self::OK:
                $msg='Ваша заявка принята.';
                break;
        }
        pEngine_Sms_Response::getInstance()->setMsg($msg)->send();
    }
}
