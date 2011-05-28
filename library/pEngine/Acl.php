<?php

class pEngine_Acl
{
    /**
     * @var pEngine_Acl
     */
    static protected $inst;

    /**
     * @var pEngine_Acl_Registry
     */
    protected $_registry=null;


    /**
     * Singlton
     * @static
     * @return pEngine_Acl
     */
    public static function getInstance(){
        return isset(self::$inst) ? self::$inst : self::$inst = new self();
    }

    /**
     * @param  $name
     * @return null|pEngine_Acl_Registry
     */
    public function __get($name){
        switch($name){
            case 'registry':
                return $this->getRegistry();
            break;
        }
    }

    /**
     * @return pEngine_Acl_Registry
     */
    public function getRegistry(){
        return isset($this->_registry) ? $this->_registry : $this->_registry = new pEngine_Acl_Registry();
    }
    /**
     * @static
     * @return pEngine_Acl_Acl
     */
    public static function factory(){
        $self = self::getInstance();
        $role = $self->registry->role;
        $self->registry->rule->setParams(func_get_args());
        $rules = $self->registry->rule->getRules($role);
        $acl = new pEngine_Acl_Acl();
        $acl->setRole($role);
        $acl->setRules($rules);
        $acl->_init();
        return $acl;
    }
}