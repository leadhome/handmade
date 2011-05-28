<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 20.04.11
 * Time: 12:54
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Acl_Rule_Ini extends pEngine_Acl_Rule_Abstract {

    /**
     * @var pEngine_Acl_Rule_Ini_Path_Abstract
     */
    protected $path=null;

    protected $rules=null;

    public function __construct(pEngine_Acl_Rule_Ini_Path_Abstract $path=null){
        $this->setPath($path);
    }
    
    public function getRules(pEngine_Acl_Role_Interface $role) {
        $file = $this->getIniFile();
        $rules = array();
        if(isset($file))
            $rules = $this->parseIniFile($file,$role);
        $this->setDefaultRule($role,$rules);

        return $rules;
    }

    protected function setDefaultRule($role,&$rules){
        if(!isset($rules['__all'])){
            $group = $role->getAllRoles();
            $rules['__all'] = new Zend_config(array('group'=>$group[0],'access'=>'allow'),true);
        }
    }

    protected function getIniFile(){
        $path = $this->path;
        $path->clearParams();

        foreach($this->params AS $param){
            $path->setParam($param);
        }

        return $path->getIniPath();
    }

    protected function parseIniFile($file,pEngine_Acl_Role_Interface $role){
        $rulesIniAcl = new Zend_Config_Ini($file,null,array('allowModifications'=>true));

        if(isset($rulesIniAcl->{$role->getName()})){
            $rulesIniAcl = $rulesIniAcl->{$role->getName()};
        }elseif(isset($rulesIniAcl->acl)){
            $rulesIniAcl = $rulesIniAcl->acl;
        }
        
        $rules = array();
        foreach($rulesIniAcl AS $key=>$value){
            switch($key){
                case 'one':
                case 'group':
                    foreach($value AS $rule){
                        switch($key){
                            case 'one':
                                $this->parseOneTypeRule($rule,$rules);
                                break;
                            case 'group':
                                $this->parseGroupTypeRule($rule,$rules);
                                break;
                        }
                    }
                    break;
                case 'all':
                    $this->parseAllTypeRule($value,$rules);
                    break;
            }
        }

        return $rules;
    }

    protected function parseOneTypeRule(Zend_Config $rule,&$rules){
        $oneRule = new Zend_Config(array(),true);
        $oneRule->group=$rule->group;
        if(isset($rule->error)){
            $oneRule->error = $rule->error;
        }

        if(isset($rule->access)){
            $oneRule->access = $rule->access;
        }else{
            $oneRule->access = 'allow';
        }

        $rules[$rule->action]=$oneRule;

        if(isset($rule->parent)){
            foreach($rule->parent AS $value){
                $rules[$value]=$oneRule;
            }
        }
    }

    protected function parseGroupTypeRule(Zend_Config $rule,&$rules){
        foreach($rule->actions AS $key=>$value){
            $oneRule = new Zend_Config(array(),true);
            $oneRule->action = $value->action;
            $oneRule->group = $rule->group;

            if(isset($value->parent)){
                $oneRule->parent=$value->parent;
            }

            if(isset($rule->access)){
                $oneRule->access=$rule->access;
            }


            if(isset($value->error)){
                $oneRule->error = $value->error;
            }elseif(isset($rule->error)){
                $oneRule->error=$rule->error;
            }

            $this->parseOneTypeRule($oneRule,$rules);
        }
    }

    protected function parseAllTypeRule(Zend_Config $rule,&$rules){
        $rule->action='__all';
        $this->parseOneTypeRule($rule,$rules);
    }



    public function setPath(pEngine_Acl_Rule_Ini_Path_Abstract $path=null){
        $this->path=$path;
        return $this;
    }
}