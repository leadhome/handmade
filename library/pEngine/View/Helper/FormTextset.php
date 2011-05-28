<?php

/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate "Textset" element
 * 
 * This will print a set of text fields
 */
class pEngine_View_Helper_FormTextset extends Zend_View_Helper_FormElement
{
	/**
	 * Generates a "textset" element.
	 *
	 * @access public
	 *
	 * @param
	 */
	public function formTextset($name, $value = null, $attribs = null)
	{
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info);

		$count = 1;
		if(isset($attribs['count'])) {
			$count = $attribs['count'];
			unset($attribs['count']);
		}

		$field_label = array();
		if(isset($attribs['field_label'])) {
			$field_label = $attribs['field_label'];
			unset($attribs['field_label']);
		}

        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
            $endTag= '>';
        }

		$xhtml = '';
		for($i = 0; $i < $count; $i++) {
			$label1 = '';
			$label2 = '';
			if(isset($field_label[$i])) {
				if(is_string($field_label[$i])) {
					$label1 = $field_label[$i];
				} elseif(is_array($field_label[$i])) {
					if(isset($field_label[$i][0])) {
						$label1 = $field_label[$i][0];
					}
					if(isset($field_label[$i][1])) {
						$label2 = $field_label[$i][1];
					}
				}
			}

			$xhtml .= $label1;
			$xhtml .= '<input type="text"'
					. ' name="' . $this->view->escape($name) . '[]"'
					. ' id="' . $this->view->escape($name) . '_' . $i . '"';
			if(isset($value[$i])) {
					$xhtml .= ' value="' . $this->view->escape($value[$i]) . '"';
			}
			$xhtml .= $this->_htmlAttribs($attribs)
					. $endTag;
			$xhtml .= $label2;
		}

        return $xhtml;
	}
}
