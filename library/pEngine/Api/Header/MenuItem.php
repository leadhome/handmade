<?php

/**
 * Class HeaderMenuItem
 * Menu item element
 *
 * @author Danilenko A.
 */
class pEngine_Api_Header_MenuItem extends pEngine_Api_Header_Object {
	/**
	 * Constructor
	 *
	 * @param confi
	 */
	public function __construct($config = array())
	{
		foreach($config as $key => $val) {
			$this->$key = $val;
		}
	}

	/**
	 * Rendering function
	 * Returns html representation of menu item
	 *
	 * @return string
	 */
	public function render()
	{
		$text = '<span class="icon ' . @$this->icon . '"></span><span class="tdu">' . @$this->text . '</span></a>';
		if(isset($this->href)) {
			$target = isset($this->href['target_blank']) ? 'target="_blank"' : '';
			$text = '<a href="' . @$this->href['link'] . '" ' . $target . ' class="' 
					. @$this->class . '" id="' . @$this->id .'">' . $text . '</a>';
		} else {
			$text = '<span class="' . @$this->class . '" id="' . @$this->id .'">' . $text . '</span>';
		}
		$html = $text;

		$description = '';
		if(isset($this->desc) && is_array(@$this->desc)) {
			foreach($this->desc as $dsc) {
				$text = @$dsc['text'];
				if(isset($dsc['href'])) {
					$target = isset($dsc['href']['target_blank']) ? 'target="_blank"' : '';
					$text = '<a href="' . $dsc['href']['link'] . '" ' . $target . '>' . $text . '</a>';
					$text .= '&nbsp;&nbsp;&nbsp;';
				}
				$description .= $text;
			}
		}
		if($description){
			$html .= '<span class="description">' . $description . '</span>';
		}

		return $html;
	}
}

