<?php

/**
 * Product_Model_Base_EditorsChoice
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $EditorsChoice_id
 * @property integer $product_id
 * @property Product_Model_Product $product
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Product_Model_Base_EditorsChoice extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('Product__Model__EditorsChoice');
        $this->hasColumn('EditorsChoice_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('product_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));


        $this->index('EC_Product', array(
             'fields' => 
             array(
              0 => 'product_id',
             ),
             ));
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'InnoDB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Product_Model_Product as product', array(
             'local' => 'product_id',
             'foreign' => 'product_id'));
    }
}