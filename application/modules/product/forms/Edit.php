<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Patsura Dmitiry <zaets28rus@gmail.com>
 * @data 2011.06.28
 */
class Product_Form_Edit
    extends ZendX_JQuery_Form
{
    public function init()
    {
        $title = new Zend_Form_Element_Text('title');
        $title->setRequired(TRUE)
              ->addFilter('StripTags')
              ->setLabel('Наименование')
              ->setDecorators(array('ViewHelper'));
        $title->addValidators(array(
                array('NotEmpty', true),
                array('stringLength', false, array(6, 255)),
        ));
         $categoriesArray = Product_Model_CategoryTable::getInstance()->getCategories(0);
        
        $categories = new Zend_Form_Element_Select('categories');
        $categories->setMultiOptions($categoriesArray)
                   ->setLabel('Выберите категорию')
                   ->setDecorators(array('ViewHelper'));
        #isset($attrs['category_id']) ? $category1->setValue($attrs['category_id']) : null;
        
        $subCategories = new Zend_Form_Element_Select('subCategories');
        $subCategories->setMultiOptions(array('Выберите категорию'))
					  ->setLabel('Выберите подкатегорию')
					  ->setDecorators(array('ViewHelper'));
        
        $price = new Zend_Form_Element_Text('price');
        $price->setRequired(TRUE)
              ->setLabel('Цена')
              ->setDecorators(array('ViewHelper'));
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->setRequired(TRUE)
                    ->setLabel('Описание товара')
                    ->setDecorators(array('ViewHelper'));
        
        $production_time = new Zend_Form_Element_Text('production_time');
        $production_time->setLabel('Врямя изготовления')
                        ->setDecorators(array('ViewHelper'));
        
        $size = new Zend_Form_Element_Text('size');
        $size->setLabel('Размеры')
             ->setDecorators(array('ViewHelper'));
        
        $availlableArray = Doctrine_Query::create()
                            ->from('Product_Model_Availlable')
                            ->execute()
                            ->toKeyValueArray('availlable_id', 'title');
        
        $availlable_id = new Zend_Form_Element_Select('availlable_id');
        $availlable_id->setMultiOptions($availlableArray)
                  ->setLabel('Укажите наличие товара')
                  ->setDecorators(array('ViewHelper'));

        $quantity = new Zend_Form_Element_Text('quantity');
        $quantity->setLabel('количество')
                 ->setDecorators(array('ViewHelper'));
               
        $submit = new Zend_Form_Element_Submit('submit_validator');
        $submit->setLabel('Отправить')
               ->setDecorators(array('ViewHelper'));
			   
			   
        $materials = new Zend_Form_Element_Hidden('materials');
        $materials->setAttrib('id','materials')
                  ->setDecorators(array('ViewHelper'));
				  
		$tags = new Zend_Form_Element_Hidden('tags');
        $tags->setAttrib('id','tags')
			 ->setDecorators(array('ViewHelper'));
		$photos = new Zend_Form_Element_Hidden('photos');
		$photos->setAttrib('id','photos')
			 ->setDecorators(array('ViewHelper'));	 
        $this->addElements(
            array(
                $title,
                $categories,
                $subCategories,
                $price,
                $description,
                $production_time,
                $size,
                $availlable_id,
                $quantity,
                $submit,
                $materials,
				$tags,
				$photos
            )
        );
    }
}
