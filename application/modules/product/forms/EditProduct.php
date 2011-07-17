<?php
/**
 * @author Patsura Dmitiry <zaets28rus@gmail.com>
 * @author Leadone
 */
class Product_Form_EditProduct
    extends ZendX_JQuery_Form
{
        public function init() {
            $this->setAction('/product/index/add')
             ->setMethod('post')
            ->setAttrib('id', 'product_form_index_add');

            $prefix = $this->getAttrib('id').'_';

            //Название товара
            $title = new Zend_Form_Element_Text('title');
            $title->setLabel('Наименование:')
                      ->setRequired(true)
                      ->setAttrib('id', $prefix.$title->getName())
                      ->setAttrib('class', 'input_create_shop')
                      ->addValidator(new Zend_Validate_NotEmpty(),array('breakChainOnFailure' => true))
                      ->addValidator(new Zend_Validate_StringLength(1, 255))
                      ->addFilter('StripTags')
                      ->addFilter('StringTrim')
                      ->setDecorators(array('ViewHelper'));

            //Главная категория
            //выбор дочерних категорий
            $categoriesArray = Product_Model_CategoryTable::getInstance()->getCategories(0);
            //создание объекта категория
            $categories = new Zend_Form_Element_Select('categories');
            $categories->setLabel('Выберите категорию:')
                               ->setRequired(true)
                               ->setMultiOptions($categoriesArray)
                               ->setAttrib('id', $prefix.$categories->getName())
                               ->setAttrib('class', 'input_create_shop')
                               ->addValidator(new Zend_Validate_NotEmpty(),array('breakChainOnFailure' => true))
                               ->addValidator(new Zend_Validate_Int())
                               ->addFilter('Int')
                               ->setDecorators(array('ViewHelper'));

            //Подкатегория
            $subCategories = new Zend_Form_Element_Select('subCategories');
            $subCategories->setLabel('Выберите подкатегорию:')
                                      ->setMultiOptions(array('Выберите категорию'))
                                      ->setAttrib('id', $prefix.$subCategories->getName())
                                      ->setAttrib('class', 'input_create_shop')
                                      ->addValidator(new Zend_Validate_NotEmpty(),array('breakChainOnFailure' => true))
                                      ->addValidator(new Zend_Validate_Int())
                                      ->addFilter('Int')
                                      ->setDecorators(array('ViewHelper'));

            //Цена
            $price = new Zend_Form_Element_Text('price');
            $price->setLabel('Цена:')
                      ->setRequired(true)
                      ->setAttrib('id', $prefix.$price->getName())
                      ->setAttrib('style', 'width:100px;')
                      ->addValidator(new Zend_Validate_NotEmpty(),array('breakChainOnFailure' => true))
                      ->addValidator(new Zend_Validate_Int())
                      ->addFilter('Int')
                      ->setDecorators(array('ViewHelper'));

            //Описание товара        
            $description = new Zend_Form_Element_Textarea('description');
            $description->setLabel('Описание товара:')
                                    ->setRequired(true)
                                    ->setAttrib('id', $prefix.$description->getName())
                                    ->addValidator(new Zend_Validate_NotEmpty())
                                    ->addFilter('StripTags')
                                    ->addFilter('StringTrim')
                                    ->setDecorators(array('ViewHelper'));

            //Врямя изготовления
            $production_time = new Zend_Form_Element_Text('production_time');
            $production_time->setLabel('Врямя изготовления:')
                                            ->setAttrib('id', $prefix.$production_time->getName())
                                            ->setAttrib('class', 'input_create_shop')
                                            ->addFilter('StripTags')
                                            ->addFilter('StringTrim')
                                            ->setDecorators(array('ViewHelper'));

            //Размеры
            $size = new Zend_Form_Element_Text('size');
            $size->setLabel('Размеры:')
                     ->setAttrib('id', $prefix.$size->getName())
                     ->setAttrib('class', 'input_create_shop')
                     ->addFilter('StripTags')
                     ->addFilter('StringTrim')
                     ->setDecorators(array('ViewHelper'));

            //Состояние товара
            $availlableArray = Product_Model_AvaillableTable::getInstance()->findAll()->toKeyValueArray('availlable_id', 'title');
            $availlable_id = new Zend_Form_Element_Select('availlable_id');
            $availlable_id->setLabel('Укажите состояние товара:')
                                      ->setAttrib('id',$prefix.$availlable_id->getName())
                                      ->setAttrib('style', 'width:294px;')
                                      ->setMultiOptions($availlableArray)
                                      ->addValidator(new Zend_Validate_Int())
                                      ->addFilter('Int')
                                      ->setDecorators(array('ViewHelper'));

            //Количество товара в наличие
            $quantity = new Zend_Form_Element_Text('quantity');
            $quantity->setAttrib('id',$prefix.$quantity->getName())
                             ->setAttrib('style', 'width:100px;')
                             ->addValidator(new Zend_Validate_Int())
                             ->addFilter('Int')
                             ->setDecorators(array('ViewHelper'));

            //Фотографии
            $photos = new Zend_Form_Element_Hidden('photos');
            $photos->setAttrib('id',$prefix.$photos->getName())
                       ->setDecorators(array('ViewHelper'));		

            //Метки товара
            //Материал
            $materials = new Zend_Form_Element_Hidden('materials');
            $materials->setAttrib('id',$prefix.$materials->getName())
                              ->setDecorators(array('ViewHelper'));

            //Тэги		
            $tags = new Zend_Form_Element_Hidden('tags');
            $tags->setAttrib('id',$prefix.$tags->getName())
                     ->setDecorators(array('ViewHelper'));

            //Submit
            $submit = new Zend_Form_Element_Submit('submit_validator');
            $submit->setLabel('Сохранить')
                       ->setDecorators(array('ViewHelper'));

            //Добавление элементов в форму
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
                    $materials,
                    $tags,
                    $photos,
                    $submit
                )
            );
    }
}
