<?php
class Inc_Decorator_ElementDecorator extends Zend_Form_Decorator_Abstract
{
	public function buildLabel() {
        $element = $this->getElement();
        $label = $element->getLabel();		
        if ($element->isRequired()) {
            $label = $label.'&nbsp;<span class="required_field_mark">*</span> ';
        }
        return $element->getView()->formLabel($element->getAttrib('id'), $label, array('escape'=>false));
    }
	public function buildInput($type=false) {
        $element = $this->getElement();
        $helper  = $element->helper;
		
		$messages = $element->getMessages();
		if (!empty($messages)) {
			$element->setAttrib('class',"not_valid");
		}
		if($type) {
			return $element->getView()->$helper($element->getName(),$element->getLabel(),$element->getAttribs(),$element->options);
		}
		
        return $element->getView()->$helper($element->getName(),$element->getValue(),$element->getAttribs(),$element->options);
    }	
	public function buildDescription() {
        $element = $this->getElement();
        $desc    = $element->getDescription();
        if (empty($desc)) {
            return '';
        }
        return '<small class="'.$element->getView()->form->getAttrib('id').'_description">'.$desc.'</small>';
    }
	public function buildErrors() {
		$element  = $this->getElement();
		
		$messages = $element->getMessages();
		if (empty($messages)) {
			return '';
		}
		return '<div class="errors">'.implode('<br/>', (array) $messages).'</div>';
	}
	public function render($content) {
		$element = $this->getElement();
		
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        if (null === $element->getView()) {
            return $content;
        }
		
        if ($element->getType() == 'Zend_Form_Element_Hidden') {
            return $content . $this->buildInput();
        } else if($element->getType()=='Zend_Form_Element_Submit' || $element->getType()=='Zend_Form_Element_Button') {
			$type		= 'button';
			$input		= $this->buildInput($type);
		} else {
			$separator 	= $this->getSeparator();
			$placement 	= $this->getPlacement();
			$label     	= $this->buildLabel();
			$input		= $this->buildInput();
			$errors   	= $this->buildErrors();
			$desc     	= $this->buildDescription();
		}
		
		
		
		$view = $element->getView();
		$view->setHelperPath('Inc/View/Helper', 'Inc_View_Helper');
		
		switch ($element->getAttrib('typeRow')) {
			case 'tr':
			default:
				$output = $element->getView()->buildTr($input, $label, $desc, $errors,$element->getView()->form->getAttrib('id'),$type);
        }	
			
        switch ($placement) {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }		
    }
}