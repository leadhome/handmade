<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Patsura Dmitiry <zaets28rus@gmail.com>
 * @data 2011.06.28
 */
class Product_Form_Product
    extends ZendX_JQuery_Form
{
    public function init()
    {
        $attrs = $this->getAttribs();
        $title = new Zend_Form_Element_Text('title');
        $title->setRequired(TRUE)
              ->addFilter('StripTags')
              ->setLabel('Наименование')
              ->setDecorators(array('ViewHelper'));
        isset($attrs['title']) ? $title->setValue($attrs['title']) : null;
                
        $category1Array = Doctrine_Query::create()
                            ->from('Product_Model_Category')
                            ->where('parent_id IS NULL')
                            ->execute()
                            ->toKeyValueArray('category_id', 'title');
        
        $category1 = new Zend_Form_Element_Select('category_level1');
        $category1->setMultiOptions($category1Array)
                  ->setLabel('Выберите категорию')
                  ->setDecorators(array('ViewHelper'));
        #isset($attrs['category_id']) ? $category1->setValue($attrs['category_id']) : null;
        
        $category2 = new Zend_Form_Element_Select('category_level2');
        $category2->setMultiOptions(array('Выбирите категорию'))
                  ->setLabel('Выберите подкатегорию')
                  ->setDecorators(array('ViewHelper'));
        
        $price = new Zend_Form_Element_Text('price');
        $price->setRequired(TRUE)
              ->setLabel('Цена')
              ->setDecorators(array('ViewHelper'));
        isset($attrs['price']) ? $price->setValue($attrs['price']) : null;
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->setRequired(TRUE)
                    ->setLabel('Описание товара')
                    ->setDecorators(array('ViewHelper'));
        isset($attrs['description']) ? $description->setValue($attrs['description']) : null;
        
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
        
        $availlable = new Zend_Form_Element_Select('availlable');
        $availlable->setMultiOptions($availlableArray)
                  ->setLabel('Укажите наличие товара')
                  ->setDecorators(array('ViewHelper'));

        $quantity = new Zend_Form_Element_Text('quantity');
        $quantity->setLabel('количество')
                 ->setDecorators(array('ViewHelper'));
               
        $submit = new Zend_Form_Element_Submit('submit_validator');
        $submit->setLabel('Отправить')
               ->setDecorators(array('ViewHelper'));
			   
			   
        $materials = new Zend_Form_Element_Hidden('materials');
        $materials->setAttrib('id','hidden_materials')
                  ->setDecorators(array('ViewHelper'));
        $this->addElements(
            array(
                $title,
                $category1,
                $category2,
                $price,
                $description,
                $production_time,
                $size,
                $availlable,
                $quantity,
                $submit,
                $materials
            )
        );
    }
}
