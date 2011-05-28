<?php

/**
 * Class Header
 * Main header constructor class
 *
 * @author Danilenko A.
 */

class pEngine_Api_Header_Header {
	private $_html = '';

    private $_color_schema = '';

	/**
	 * Constructor
	 *
	 * @param array config
	 * @param JUser user
	 */
	public function __construct($config = array(), $user = null)
	{
		if(!isset($config['header'])) {
			return;
		}

        $this->_color_schema = @$config['header']['color']['schema'];

		foreach($config['header']['items'] as $key => $item) {
			if(!isset($item['type'])) {
				continue;
			}
			$object = null;
			switch($item['type']) {
				case 'link':
					$object = new pEngine_Api_Header_Link($item);
					break;
				case 'menu':
					$object = new pEngine_Api_Header_Menu($item);
					break;
				case 'separator':
					$object = new pEngine_Api_Header_Separator($item);
					break;
			}
			if($object) {
				$this->_html .= $object->render();
			}
		}
		$object = new pEngine_Api_Header_AuthItem($config['header']['user'], $user);
		$this->_html .= $object->render();
	}

	/**
	 * Rendering function
	 * Returns html representation of $this object
	 *
	 * @return string
	 */
	public function render()
	{
		$html = '<div class="amurnet-menu ' . $this->_color_schema . '"><div class="content">' . $this->_html . '</div></div>';

		return $html;
	}
}
