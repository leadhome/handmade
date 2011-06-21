<?php

/**
 * Product_Model_Base_TagProduct
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $tagProduct_id
 * @property integer $product_id
 * @property integer $tag_id
 * @property integer $user_id
 * @property Product_Model_Product $Product
 * @property Product_Model_Tag $Tag
 * @property User_Model_User $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Product_Model_Base_TagProduct extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('Product__Model__TagProducts');
        $this->hasColumn('tagProduct_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('product_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('tag_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));


        $this->index('TP_Product', array(
             'fields' => 
             array(
              0 => 'product_id',
             ),
             ));
        $this->index('TP_Tag', array(
             'fields' => 
             array(
              0 => 'tag_id',
             ),
             ));
        $this->index('TP_User', array(
             'fields' => 
             array(
              0 => 'user_id',
             ),
             ));
        $this->index('TagProductUser', array(
             'fields' => 
             array(
              'product_id' => 
              array(
              'sorting' => 'ASC',
              ),
              'tag_id' => 
              array(
              'sorting' => 'ASC',
              ),
              'user_id' => 
              array(
              'sorting' => 'ASC',
              ),
             ),
             'type' => 'unique',
             ));
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'InnoDB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Product_Model_Product as Product', array(
             'local' => 'product_id',
             'foreign' => 'product_id'));

        $this->hasOne('Product_Model_Tag as Tag', array(
             'local' => 'tag_id',
             'foreign' => 'tag_id'));

        $this->hasOne('User_Model_User as User', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));
    }
}