<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 26.04.11
 * Time: 10:51
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Acl_Error_Message_Zend extends pEngine_Acl_Error_Message_Abstract{
    /**
     * Start error
     * @return void
     */
    public function perform()
    {
        JError::raiseError( $this->code, JText::_($this->message) );
    }
}