<?php

/**
 * Класс HeaderLink
 * Блок пользователя
 *
 * @author Даниленко А.
 */
class pEngine_Api_Header_AuthItem extends pEngine_Api_Header_Object {
	private $_user = null;

    private $_config = null;
	/**
	 * Функция рендеринга AuthItem
	 *
	 * @return string
	 */
	public function render()
	{
		$html = '';
		if($this->_user) {
            if($this->_config['person']['enable'] == true) {
                $this->_config['person']['title']['text']['string'] = $this->_user['name'];
                $object = new pEngine_Api_Header_Menu($this->_config['person']);
                $html = $object->render();
            }
		} else {
            if($this->_config['register']['enable'] == true) {
                $object = new pEngine_Api_Header_Link($this->_config['register']);
                $html = $object->render();
            }

            if($this->_config['login']['enable'] == true) {
                $object = new pEngine_Api_Header_Link($this->_config['login']);
                $html .= $object->render();
            }
		}
		return $html;
	}

	/**
	 * Конструктор
	 *
	 */
	public function __construct($config = null, $user = null)
	{
        $this->_config = $config;
        $this->_user = Zend_Json::decode(base64_decode($user));
//		$this->_user = $user;
	}
}
