<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 11.05.11
 * Time: 14:49
 * To change this template use File | Settings | File Templates.
 */
 
abstract class pEngine_Qiwi_Observer{


    public function __construct($qiwi){
        $qiwi->attach($this);
    }

    abstract function update($subject);
}