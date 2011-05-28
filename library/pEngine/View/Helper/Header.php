<?php

class pEngine_View_Helper_Header extends Zend_View_Helper_Abstract {

    public function header()
    {
        $user = null;
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            if(get_class($identity) == 'jUser') {
                $user = $identity;
            }
        }

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/header.ini', 'default');
        $config = $config->toArray();

        $json = json_encode($config);
        $json = base64_encode($json);
        $header = pEngine_Api::factory()
                    ->setMethod('header.getHeader')
                    ->setParam('config', $json)
                    ->setParam('session', @$session)
                    ->setParam('user', base64_encode(Zend_Json::encode($user)))
                    ->query();
        return $header;
    }

}
