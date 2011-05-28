<?php

class Admin_Form_MoveCategory extends Zend_Form
{

    public function init()
    {
        $categories = Magazine_Model_Category::getCategories();
		if($categories){
			$from_category = new Zend_Form_Element_Select('from_category');
			$from_category->setLabel('Category:');
			$from_category->addValidator(new Zend_Validate_Alnum());
			$from_category->addFilter(new Zend_Filter_StripTags);
			foreach($categories as $c)
				$from_category->addMultiOption($c->id, str_repeat('-', $c->level - 1) . ' ' . $c->title);

			$to_category = new Zend_Form_Element_Select('to_category');
			$to_category->setLabel('To category:');
			$to_category->addValidator(new Zend_Validate_Alnum());
			$to_category->addFilter(new Zend_Filter_StripTags);
//			$to_category->addMultiOption(1, 'Magazines');
			foreach($categories as $c)
				$to_category->addMultiOption($c->id, str_repeat('-', $c->level - 1) . ' ' . $c->title);

			$submit = new Zend_Form_Element_Submit('submit');
			$submit->setLabel('move');
			$submit->setRequired(true);

			$this->addElements(array($from_category, $to_category, $submit));

		}
    }


}

