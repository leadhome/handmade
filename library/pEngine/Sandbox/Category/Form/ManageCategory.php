<?php

class pEngine_Category_Form_ManageCategory extends Zend_Form
{
    public function init()
    {
		$categories_values = Spravka_Model_CategoryTable::getInstance()->getCategories();
        $categories = new Zend_Form_Element_Multiselect('categories');
		$categories->setLabel('Categories');
		foreach($categories_values as $c)
			$categories->addMultiOption($c->id, str_repeat('-', $c->level - 1) . ' ' . $c->title);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Delete');

		$this->addElements(array($categories, $submit));
    }

}

