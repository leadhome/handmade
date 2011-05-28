<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Plugin
 *
 * @author yura
 */
class pEngine_Auth_Amurnet_Plugin extends Zend_Controller_Plugin_Abstract{

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request){
        if(!Zend_Auth::getInstance()->hasIdentity()){
            if(isset($_SESSION['__default']['user'])){
                $user  = $_SESSION['__default']['user'];
                if($user->id)
                    Zend_Auth::getInstance()->authenticate(new pEngine_Auth_Amurnet_Adapter());
            }
        }
    }
}
// Jommla Class User
class jUser extends stdClass{}