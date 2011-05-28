<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 19.04.11
 * Time: 12:15
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Acl_Registry{
    protected $roles=array();

    /**
     * @var pEngine_Acl_Rule_Abstract
     */
    protected $rule;


    protected $roleNameDefault='';

    protected $error=array();
    
    /**
     * @var pEngine_Acl_Config
     */
    static protected $inst;


    /**
     * Singlton
     * @static
     * @return pEngine_Acl_Registry
     */
    public static function getInstance(){
        return isset(self::$inst) ? self::$inst : self::$inst = new self();
    }

    public function __get($name){
        switch($name){
            case 'role':
                return $this->selectRole();
                break;
            case 'rule':
                return $this->rule;
                break;
        }
        return null;
    }

    protected function getAcl(){

    }



    protected function selectRole(){
        foreach($this->roles AS $role){
            if($role->isAuth()){
                return $this->roleNow = $role;
            }
        }

        return $this->roleNow = new pEngine_Acl_Role_Default();
    }

    public function setRole(pEngine_Acl_Role_Interface $role){
        array_push($this->roles,$role);
        return $this;
    }

    public function setArrayRole($roles){
        foreach($roles AS $role){
           $this->setRole($role);
        }
        return $this;
    }

    public function cleanRoles(){
        $this->roles=array();
        return $this;
    }

    public function setRule(pEngine_Acl_Rule_Abstract $rule){
        $this->rule = $rule;
        return $this;
    }

    public function getRoleNameDefault(){
        return $this->roleNameDefault;
    }

    public function setRoleNameDefault($name=''){
        $this->roleNameDefault = $name;
    }
    /**
     * @param  $name
     * @param pEngine_Acl_Error_Abstract $error
     * @return pEngine_Acl_Registry
     */
    public function setError($name,pEngine_Acl_Error_Abstract $error){
        $this->error[$name]=$error;
        $this;
    }

    /**
     * @throws Exception
     * @param  $name
     * @return pEngine_Acl_Error_Abstract
     */
    public function getError($name){
        if(!isset($this->error[$name])){
            throw new Exception('Not Registry Error with name: '.$name);
        }

        return $this->error[$name];
    }

}