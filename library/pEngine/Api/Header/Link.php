<?php

/**
 * Class HeaderLink
 * Simple link in the header. It can contain an icon.
 *
 * @author Danilenko A.
 */
class pEngine_Api_Header_Link extends pEngine_Api_Header_Object {
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
	 * Returns html representation of $this object
	 *
	 * @return string
	 */
	public function render()
	{
		$html = '';
		$target = isset($this->href['target_blank']) ? 'target="_blank"' : '';
		
		$html = '<a href=' . @$this->href['link'] . 
			' class="tdn ' . @$this->class . '" id = "' . @$this->id . '" ' . $target . '>';
		$html .= '<span class = "icon ' . @$this->icon . '"></span>';
		$html .= '<span class = "tdu">' . @$this->text . '</span>';
		$html .= '</a>';
		return $html;
	}
}
