<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Patsura Dmitiry <zaets28rus@gmail.com>
 * @data 2011.06.28
 */
class Product_Form_Add
    extends ZendX_JQuery_Form
{
    public function init()
    {
        $title = new Zend_Form_Element_Text('title');
        $title->setRequired(TRUE)
              ->addFilter('StripTags')
              ->setLabel('Наименование')
              ->setDecorators(array('ViewHelper'));
        
        $category1Array = Doctrine_Query::create()
                            ->from('Product_Model_Category')
                            ->where('parent_id IS NULL')
                            ->execute()
                            ->toKeyValueArray('category_id', 'title');
        
        $category1 = new Zend_Form_Element_Select('category_level1');
        $category1->setMultiOptions($category1Array)
                  ->setLabel('Выберите категорию')
                  ->setDecorators(array('ViewHelper'));
        
        $category2 = new Zend_Form_Element_Select('category_level2');
        $category2->setMultiOptions(array('Выбирите категорию'))
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
        
        $availlable = new Zend_Form_Element_Select('availlable');
        $availlable->setMultiOptions($availlableArray)
                  ->setLabel('Укажите наличие товара')
                  ->setDecorators(array('ViewHelper'));

        $quantity = new Zend_Form_Element_Text('quantity');
        $quantity->setLabel('количество')
                 ->setDecorators(array('ViewHelper'));
        
        $colorsArray = Doctrine_Query::create()
                            ->from('Product_Model_Color')
                            ->orderBy('color_id asc')
                            ->execute()
                            ->toKeyValueArray('color_id', 'title');
        
        $color1 = new Zend_Form_Element_Select('color1');
        $color1->setMultiOptions($colorsArray)
               ->setLabel('Цвет Товара')
               ->setRequired(false)
               ->setDecorators(array('ViewHelper'));
        
        $color1->setValue($colorsArray[1]);

        $color2 = new Zend_Form_Element_Select('color2');
        $color2->setMultiOptions($colorsArray)
               ->setLabel('Цвет Товара')
               ->setRequired(false)
               ->setDecorators(array('ViewHelper'));
        
        $color2->setValue($colorsArray[2]);
                
        $color3 = new Zend_Form_Element_Select('color3');
        $color3->setMultiOptions($colorsArray)
               ->setLabel('Цвет Товара')
               ->setRequired(false)
               ->setDecorators(array('ViewHelper'));
        
        $color3->setValue($colorsArray[3]);
             
        $submit = new Zend_Form_Element_Submit('submit_validator');
        $submit->setLabel('Отправить')
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
                $color1,
                $color2,
                $color3,
                $submit
            )
        );
    }
}
