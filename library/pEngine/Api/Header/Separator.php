<?php

/**
 * Class HeaderSeparator
 * Header items separator
 *
 * @author Danilenko A.
 */
class pEngine_Api_Header_Separator extends pEngine_Api_Header_Object {
	/**
	 * Constructor
	 *
	 * @param config
	 */
	public function __construct($config = array())
	{
		foreach($config as $key => $val) {
			$this->$key = $val;
		}
	}

	/**
	 * Rendering function
	 * Returns html representation of separator
	 *
	 * @return string
	 */
	public function render()
	{
		$html = '<span class="separator"></span>';

		return $html;
	}
}
