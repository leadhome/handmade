<?php
/**
 * Class HeaderObject
 * Abstract header object class, that contains render() function
 *
 * @author Danilenko A.
 */
abstract class pEngine_Api_Header_Object {
	/**
	 * Rendering function
	 * Returns html representation of $this object
	 *
	 * @return string
	 */
	public abstract function render();
}
