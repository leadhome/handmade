<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 23.05.11
 * Time: 16:41
 * To change this template use File | Settings | File Templates.
 */
 
abstract class pEngine_SmsSend_Transport_Abstract{

    protected $option=array();

    public function __construct($option){
        $this->option = $option;
    }

    abstract public function send($from,$to,$body,$charset);
}