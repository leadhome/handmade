<?php
class Admin_Form_EditCategory extends Zend_Form
{
	public function init()
	{
		$this->setAction("");
		$this->setMethod("POST");

		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Name:');
		$name->addValidator(new Zend_Validate_StringLength(0, 128));
		$name->addValidator(new pEngine_Validator_Exist('Magazine_Model_Category', 'name'));
		$name->addFilter(new Zend_Filter_StripTags);
		$name->setRequired(true);

		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Title:');
		$title->addValidator(new Zend_Validate_StringLength(0, 128));
		$title->addFilter(new Zend_Filter_StripTags);
		$title->setRequired(true);

		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel('Description:');
		$description->setRequired(true);

		$uri = new Zend_Form_Element_Text('uri');
		$uri->setLabel('Static url:');
//		$uri->addValidator(new Zend_Uri());
		$uri->addFilter(new Zend_Filter_StripTags);
		$uri->setRequired(true);

		$order = new Zend_Form_Element_Text('order');
		$order->setLabel('Order:');
		$order->addValidator(new Zend_Validate_StringLength(0, 3));
		$order->addFilter(new Zend_Filter_StripTags);
		$order->setRequired(true);

		$publish = new Zend_Form_Element_Checkbox('publish');
		$publish->setLabel('Publish:');
		$publish->setChecked(true);

		$target = new Zend_Form_Element_Checkbox('target');
		$target->setLabel('Open as new page:');
		$target->setChecked(false);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Submit');
		$submit->setRequired(true);

		$attr = $this->getAttribs();
//		Zend_Debug::dump($attr);
//		die();
		if(isset($attr)){
			$name->setValue($attr['name']);
			$title->setValue($attr['title']);
			$description->setValue($attr['_description']);
			$uri->setValue($attr['uri']);
			$order->setValue($attr['_order']);
			$publish->setValue($attr['publish']);
			$target->setValue($attr['target']);
		}

		$this->addElements(array($name, $title, $description, $uri, $order, $publish, $target, $submit));
	}
}