<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 16.04.11
 * Time: 12:09
 * To change this template use File | Settings | File Templates.
 */
 class pEngine_Acl_Role_Zend_Joomla extends pEngine_Acl_Role_Abstract{

    protected $name='joomla';

    protected $groupes;

    public function isAuth(){
        if(Zend_Auth::getInstance()->hasIdentity()){
            return (get_class(Zend_Auth::getInstance()->getIdentity())=='JUser') ? true : false;
        }
    }

    public function getAllRoles(){
        $this->groupes = pEngine_Api::factory()->setMethod('group.getall')->query();
        return $this->_getLineGroupes($this->groupes,Zend_Auth::getInstance()->getIdentity()->gid);
    }

    public function getNowRole(){
        $gid = Zend_Auth::getInstance()->getIdentity()->gid;
        foreach($this->groupes AS $group){
            if($group->id==$gid){
                return $group->name;
            }
        }
    }

    protected function _getLineGroupes($groupes,$gid){
        foreach($groupes AS $group){
            if($group->id==$gid){
                $name = $group->name;
                $parent = $this->_getLineGroupes($groupes,$group->parent_id);
                $parent[]=$name;
                return $parent;
            }
        }
        return array();
    }
}
