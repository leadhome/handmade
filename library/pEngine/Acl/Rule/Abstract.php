<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 18.04.11
 * Time: 12:27
 * To change this template use File | Settings | File Templates.
 */
abstract class pEngine_Acl_Rule_Abstract{

    protected $params=array();
    
    abstract public function getRules(pEngine_Acl_Role_Interface $role);

    public function setParams(array $params){
        $this->params = $params;
    }
}
