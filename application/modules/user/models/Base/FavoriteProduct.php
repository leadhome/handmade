<?php

/**
 * User_Model_Base_FavoriteProduct
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $favoriteProduct_id
 * @property integer $product_id
 * @property integer $user_id
 * @property Product_Model_Product $Product
 * @property User_Model_User $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class User_Model_Base_FavoriteProduct extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('User__Model__FavoriteProducts');
        $this->hasColumn('favoriteProduct_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('product_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));


        $this->index('productUser', array(
             'fields' => 
             array(
              'product_id' => 
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
        $this->index('FP_Product', array(
             'fields' => 
             array(
              0 => 'product_id',
             ),
             ));
        $this->index('FP_User', array(
             'fields' => 
             array(
              0 => 'user_id',
             ),
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

        $this->hasOne('User_Model_User as User', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));
    }
}