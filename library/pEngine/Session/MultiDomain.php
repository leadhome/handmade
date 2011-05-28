<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MultiDomain
 *
 * @author yura
 */



class pEngine_Session_MultiDomain {
    const SESSION_JOOMLA_NAMESPACE='__default';
    protected static $_defDomain;

    public static function session($domain = "amur.net"){
	self::$_defDomain=$domain;
        if(!isset($_SESSION[self::SESSION_JOOMLA_NAMESPACE])){
            self::authentication();
        }
    }

    protected static function authentication(){
        $sessionName = ini_get('session.name');

        Zend_Controller_Action_HelperBroker::getStaticHelper( 'ViewRenderer')
            ->view
            ->headScript()
            ->appendFile('http://'.self::$_defDomain.'/?option=com_ajax&task=authentication&name='.$sessionName);
    }
}
