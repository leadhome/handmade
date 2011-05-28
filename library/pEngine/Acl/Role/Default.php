<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 18.04.11
 * Time: 14:20
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Acl_Role_Default extends pEngine_Acl_Role_Abstract{

   protected $name='guest';

   public function isAuth(){
       return true;
   }

   public function getAllRoles(){
       return array('Guest');
   }

   public function getNowRole(){
       return 'Guest';
   }
}