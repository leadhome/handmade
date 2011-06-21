<?php

/**
 * User_Model_Base_DeliveryShop
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $shopDelivery_id
 * @property integer $shop_id
 * @property integer $delivery_id
 * @property integer $price_delivery
 * @property User_Model_Shop $Shop
 * @property User_Model_Delivery $Delivery
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class User_Model_Base_DeliveryShop extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('User__Model__DeliveryShops');
        $this->hasColumn('shopDelivery_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('shop_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('delivery_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('price_delivery', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));


        $this->index('shopDelivery', array(
             'fields' => 
             array(
              'shop_id' => 
              array(
              'sorting' => 'ASC',
              ),
              'delivery_id' => 
              array(
              'sorting' => 'ASC',
              ),
             ),
             'type' => 'unique',
             ));
        $this->index('DS_Shop', array(
             'fields' => 
             array(
              0 => 'shop_id',
             ),
             ));
        $this->index('DS_Delivery', array(
             'fields' => 
             array(
              0 => 'delivery_id',
             ),
             ));
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'InnoDB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('User_Model_Shop as Shop', array(
             'local' => 'shop_id',
             'foreign' => 'shop_id'));

        $this->hasOne('User_Model_Delivery as Delivery', array(
             'local' => 'delivery_id',
             'foreign' => 'delivery_id'));
    }
}