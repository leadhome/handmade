<?php
class pEngine_Category_Form_EditCategory extends Zend_Form
{
	public function init()
	{
		$this->setAction("");
		$this->setMethod("POST");

		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Name:');
		$name->addValidator(new Zend_Validate_StringLength(0, 128));
		$name->addValidator(new pEngine_Validator_Exist('Spravka_Model_Category', 'name'));
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

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Submit');
		$submit->setRequired(true);

		$attr = $this->getAttribs();
		if(isset($attr)){
			$name->setValue($attr['name']);
			$title->setValue($attr['title']);
			$description->setValue($attr['_description']);
		}

		$this->addElements(array($name, $title, $description, $submit));
	}
}