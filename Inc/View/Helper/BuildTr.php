<?php
class Inc_View_Helper_BuildTr extends Zend_View_Helper_Abstract
{
	
    public function buildTr($input, $label, $description, $errors,$form_id,$type=false) {
      
		 return 
			'<tr>
				<td class="'.$form_id.'_label">'.$label.'</td>
				<td class="'.$form_id.'_element">'.$input.$description.'</td>
				<td id="'.$form_id.'_errors">'.($errors ? $errors : '&nbsp;').'</td>
            </tr>';
        
    }

}