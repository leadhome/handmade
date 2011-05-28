<?php

/**
 * Class HeaderMenu
 *
 * @author Danilenko A.
 */
class pEngine_Api_Header_Menu extends pEngine_Api_Header_Object {
	/**
	 * Constructor
	 * Config contains both menu and menu items description
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
	 * Returns html representation of menu and menu items
	 *
	 * @return string
	 */
	public function render()
	{
		$html = '';

		$html .= '<span class="submenu"> <span class="title"> ';
		$text = '<span class="icon ' . @$this->icon . '"></span>' 
				. '<span class="' . @$this->title['text']['class'] . '">' . @$this->title['text']['string'] . '</span>';
		if(isset($this->href)) {
			$target = isset($this->href['target_blank']) ? 'target="_blank"' : '';
			$text = '<a href=' . $this->href['link'] . ' ' . $target . '>' . $text . '</a>';
		}
		$html .= $text;
		
		$html .= '<span class="arrow"></span> </span>';
		$html .= '<span class="shadow-blocker"></span>';
			$html .= '<span class="content">';
			if(isset($this->items) && is_array(@$this->items)) {
				foreach($this->items as $item) {
					$object = new pEngine_Api_Header_MenuItem($item);
					$html .= $object->render();
				}
			}
			$html .= '</span>';
		$html .= '</span>';

		return $html;
	}
}


