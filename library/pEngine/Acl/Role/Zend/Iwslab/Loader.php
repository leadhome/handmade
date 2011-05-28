<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 16.04.11
 * Time: 11:46
 * To change this template use File | Settings | File Templates.
 */
 
abstract class pEngine_Acl_Role_Zend_Iwslab_Loader {

    /**
     * @var Object
     */
    protected $user;
    /**
     * Return Array All Grope
     * @abstract
     * @param  $id Id User
     * @return Array
     */
    abstract public function getRoles();


    /**
     * Return groupe in User
     * @abstract
     * @param  $id Id User
     * @return String
     */
    abstract public function getRole();

    /**
     * Set User
     * @param  $user Object
     * @return pEngine_Acl_Role_Zend_Iwslab_Loader
     */
    public function setUser($user){
        $this->user = $user;
        return $this;
    }
}