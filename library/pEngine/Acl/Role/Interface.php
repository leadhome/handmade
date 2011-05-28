<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 16.04.11
 * Time: 11:30
 * To change this template use File | Settings | File Templates.
 */
 
interface pEngine_Acl_Role_Interface{

    public function getName();

    public function isAuth();

    public function getAllRoles();

    public function getNowRole();
}