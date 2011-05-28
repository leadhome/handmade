<?php

class pEngine_Mail_ObCli implements pEngine_Observable_Cli
{
    const USER_REGISTRATION = "userRegistration";
    const FORGOT_YOUR_PASSWORD = "forgotPassword";
    const NEW_PASSWORD = "newPassword";

    /**
     *
     * $param sample:
     *
     * array ('method' => pEngine_Mail_ObCli::USER_REGISTRATION_TRUE, 'name' => 'username', 'email' => 'email@mail.ru',
     * 'activation_url' => 'http:\\sample', 'activation_code' => '324rewr324', 'password' => 'newpassword')
     *
     *
     *
     * @param pEngine_Mail_Mail $obj
     * @param array $param
     */
    public function notify($obj, $param)
    {
        if (!($obj instanceof pEngine_Mail_Mail))
        {
            return false;
        }

        if (method_exists($this, $param['method']))
        {
            $this->$param['method']($obj, $param);

        }
    }

    /**
     * send activation url
     *
     * $param sample:
     * array('method' => 'method name', 'name' => 'user name', 'email' => 'user email', 'activation_url' => 'url')
     * 
     * @param pEngine_Mail_Mail $obj
     * @param array $param
     */
    private function userRegistration($obj, $param)
    {
        $recipients[] = array('name' => $param['name'], 'email' => $param['email']);

        $data = Doctrine_Core::getTable("Mail_Model_Content")->findOneById(1);
        $data['text'] = str_replace('~user~', $param['name'], $data['text']);
        $data['text'] = str_replace('~server~', Zend_Controller_Front::getInstance()->getRequest()->getServer('SERVER_NAME'), $data['text']);
        $data['text'] = str_replace('~url~', $param['activation_url'], $data['text']);

        $obj->send($recipients, $data['subject'], $data['text']);
    }

    /**
     * send activation code
     *
     * $param sample:
     * array('method' => 'method name', 'name' => 'user name', 'email' => 'user email', 'activation_code' => 'code')
     *
     * @param pEngine_Mail_Mail $obj
     * @param array $param
     */
    private function forgotPassword($obj, $param)
    {
        $recipients[] = array('name' => $param['name'], 'email' => $param['email']);

        $data = Doctrine_Core::getTable("Mail_Model_Content")->findOneById(2);
        $data['text'] = str_replace('~user~', $param['name'], $data['text']);
        $data['text'] = str_replace('~server~', Zend_Controller_Front::getInstance()->getRequest()->getServer('SERVER_NAME'), $data['text']);
        $data['text'] = str_replace('~code~', $param['activation_code'], $data['text']);

        $obj->send($recipients, $data['subject'], $data['text']);
    }

    /**
     * send new password
     *
     * $param sample:
     * array('method' => 'method name', 'name' => 'user name', 'email' => 'user email', 'password' => 'newpassword')
     *
     * @param pEngine_Mail_Mail $obj
     * @param array $param
     */
    private function newPassword($obj, $param)
    {
        $recipients[] = array('name' => $param['name'], 'email' => $param['email']);

        $data = Doctrine_Core::getTable("Mail_Model_Content")->findOneById(3);
        $data['text'] = str_replace('~user~', $param['name'], $data['text']);
        $data['text'] = str_replace('~server~', Zend_Controller_Front::getInstance()->getRequest()->getServer('SERVER_NAME'), $data['text']);
        $data['text'] = str_replace('~password~', $param['password'], $data['text']);

        $obj->send($recipients, $data['subject'], $data['text']);
    }
}