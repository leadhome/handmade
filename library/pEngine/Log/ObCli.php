<?php

class pEngine_Log_ObCli implements pEngine_Observable_Cli
{

    /**
    * User login correct
    */
    const USER_LOGIN_TRUE  = 'userLoginTrue';

    /**
     * User login incorrect
     */
    const USER_LOGIN_FALSE = 'userLoginFalse';

    
    public function notify($obj, $param)
    {
        if (!($obj instanceof pEngine_Log_Log))
            return false;

        if (method_exists($this, $param))
            $this->$param($obj);
    }

    /**
     * Save login user in log
     * @param pEngine_Log_Log $obj
     * @return void
     */
    private function userLoginTrue(pEngine_Log_Log $obj)
    {
        //delete the entry in the obj
        $obj->delete();
        //write successful authorization
        $obj->setCode(2); //successful authorization code
        $obj->setUserId(Zend_Auth::getInstance()->getIdentity()->id);
        $obj->save();
    }

    /**
     * Save failure login
     * @param pEngine_Log_Log $obj
     * @return void
     */
    private function userLoginFalse(pEngine_Log_Log $obj)
    {
        $obj->setCode(1);
        $obj->save();
    }

    
}