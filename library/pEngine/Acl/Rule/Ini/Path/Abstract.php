<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 20.04.11
 * Time: 13:14
 * To change this template use File | Settings | File Templates.
 */
 
abstract class pEngine_Acl_Rule_Ini_Path_Abstract{
    protected $params=array();

    protected $roleName=null;

    abstract public function setParam($value=null);

    public function clearParams(){
        $this->params = array();
        return $this;
    }

    public function setRoleName($name=null){
        $this->roleName = $name;
    }
    
    abstract public function getIniPath();
}