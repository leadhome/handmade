<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Adapter
 *
 * @author yura
 */
class pEngine_Auth_Amurnet_Adapter  implements Zend_Auth_Adapter_Interface{
    public function authenticate() {
        if(isset($_SESSION['__default']['user'])){
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $_SESSION['__default']['user'], array());
        }else{
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null, array('Not user'));
        }
    }
}