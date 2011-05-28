<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 25.04.11
 * Time: 11:26
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Acl_Acl{

    /**
     * @var Zend_Acl
     */
    protected $zendAcl = null;

    /**
     * @var pEngine_Acl_Role_Interface
     */
    protected $role=null;

    /**
     * @var array
     */
    protected $rules=null;

    /**
     * @param  $role pEngine_Acl_Role_Interface
     * @return void
     */
    public function setRole($role){
        $this->role = $role;
        return $this;
    }
    /**
     * @param  $rules array
     * @return pEngine_Acl_Acl
     */
    public function setRules($rules){
        $this->rules = $rules;
        return $this;
    }

    /**
     * @return pEngine_Acl_Acl
     */
    public function _init(){
        $this->zendAcl = new Zend_Acl();

        // init role
        $groupes = $this->role->getAllRoles();
        $parent=null;
        foreach($groupes AS $group){
            $this->zendAcl->addRole(new Zend_Acl_Role($group),$parent);
            $parent = array($group);
        }

        // init rules
        foreach($this->rules AS $resource=>$rule){
            $this->zendAcl->addResource(new Zend_Acl_Resource($resource));
            if($this->zendAcl->hasRole($rule->group)){
                if($rule->access=='allow'){
                    $this->zendAcl->allow($rule->group,$resource);
                }elseif($rule->access=='deny'){
                    $this->zendAcl->deny($rule->group,$resource);
                }
            }
        }

        return $this;
    }
    /**
     * @deprecated  Устаревшая функция, нужна для поддержки кода
     * @return pEngine_Acl_Acl
     */
    public function init(){
        return $this;
    }

    protected function getRuleName($ruleName){
        if(!$this->zendAcl->has($ruleName)){
            $ruleName = '__all';
        }

        return $ruleName;
    }

    protected function getRoleName(){
        return $this->role->getNowRole();
    }

    public function isAllowed($ruleName){
        return $this->zendAcl->isAllowed($this->getRoleName(),$this->getRuleName($ruleName));
    }

    public function access($ruleName){
        if($this->isAllowed($ruleName))
            return true;
        $ruleName = $this->getRuleName($ruleName);
        if(isset($this->rules[$ruleName]->error)){
            $errorParams = $this->rules[$ruleName]->error->toArray();
            $errorName = $this->rules[$ruleName]->error->type;
        }else{
            $errorParams = array();
            $errorName = 'message';
        }
        $error = pEngine_Acl::getInstance()->getRegistry()->getError($errorName);
        if(isset($error)){
            $error->setParams($errorParams)->perform();
        }
    }
}