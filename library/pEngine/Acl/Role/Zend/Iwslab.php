<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 16.04.11
 * Time: 11:28
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Acl_Role_Zend_Iwslab extends pEngine_Acl_Role_Abstract{

    private $name='iwslab';

    /**
     * @var \pEngine_Acl_Role_Zend_Iwslab_Loader
     */
    protected $inst;

    function __construct(pEngine_Acl_Role_Zend_Iwslab_Loader $inst){
        $this->inst = $inst;
    }

    public function isAuth(){
        return Zend_Auth::getInstance()->hasIdentity();
    }

    public function getAllRoles(){
        return $this->inst->setUser(Zend_Auth::getInstance()->getIdentity())->getRoles();
    }

    public function getNowRole(){
        return $this->inst->setUser(Zend_Auth::getInstance()->getIdentity())->getRole();
    }
}