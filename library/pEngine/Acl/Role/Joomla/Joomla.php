<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 16.04.11
 * Time: 12:09
 * To change this template use File | Settings | File Templates.
 */
 class pEngine_Acl_Role_Joomla_Joomla extends pEngine_Acl_Role_Abstract{

    protected $name='joomla';

    protected $groupes;

    public function isAuth(){
        return JFactory::getUser()->isGuest() ? false : true;
    }

    public function getAllRoles(){
        $db = JFactory::getDBO();
        $query = 'SELECT `id`,`parent_id`,`name` FROM  jos_core_acl_aro_groups';
        $db->setQuery($query);
        $this->groupes = $db->loadOjectList();
        
        return $this->_getLineGroupes($this->groupes,JFactory::getUser()->gid);
    }

    public function getNowRole(){
        return JFactory::getUser()->usertype;
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
