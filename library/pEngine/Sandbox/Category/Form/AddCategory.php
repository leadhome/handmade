<?php
class pEngine_Category_Form_AddCategory extends Zend_Form
{
	public function init()
	{
		$this->setAction("");
		$this->setMethod("POST");

		$category = Spravka_Model_CategoryTable::getInstance();

		$categories = $category->getCategories();
		$category = new Zend_Form_Element_Select('category');
		$category->setLabel('Parent category:');
		$category->addValidator(new Zend_Validate_Alnum());
		$category->addFilter(new Zend_Filter_StripTags);
		$category->addMultiOption(1, 'none');
		if($categories){
			foreach($categories as $c)
				$category->addMultiOption($c->id, str_repeat('- ', $c->level - 1) . ' ' . $c->title);
		}

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
//		$description->setRequired(true);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Submit');
		$submit->setRequired(true);

		$this->addElements(array(isset($category) ? $category : null, $name, $title, $description, $submit));
	}
}