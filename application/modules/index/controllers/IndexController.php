<?php

class Index_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
	public function headerAction() {
		
		$auth = Zend_Auth::getInstance();
		$locationUser = new Zend_Session_Namespace('locationUser');
		
		//определение местоположения пользователя
		if(!$auth->hasIdentity()) {
			if(!$locationUser->city_id) {
				$geoIp = Inc_Geo_Ip::getInstance();
				$infoIp = $geoIp->getInfo($_SERVER['REMOTE_ADDR']);
				$user = User_Model_CityTable::getInstance()->getCity($infoIp);
				
				foreach($user as $key=>$value) {
					$locationUser->$key = $value;
				}
			}
		} 
		$this->view->user = $auth->getIdentity();
		$this->view->locationUser = $locationUser;
	}
}

