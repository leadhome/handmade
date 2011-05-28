<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 26.04.11
 * Time: 10:44
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Acl_Rule_Ini_Path_Joomla extends pEngine_Acl_Rule_Ini_Path_Abstract{


    public function getIniPath()
    {
        if(!isset($this->params['option'])){
            $this->params['option'] = JRequest::getCMD('option');
        }

        $file = JPATH_BASE.DS.'components'.DS.$this->params['option'].DS.'acl.ini';
        return is_readable($file) ? $file : null;

    }


    public function setParam($value = null)
    {
        switch(count($this->params)){
            case 0:
                $this->params['option']=$value;
            break;
        }
        return $value;
    }
}