<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 16.04.11
 * Time: 11:34
 * To change this template use File | Settings | File Templates.
 */
 
abstract class pEngine_Acl_Role_Abstract implements pEngine_Acl_Role_Interface{
    protected $name;
    
    public function getName(){
        return $this->name;
    }
}