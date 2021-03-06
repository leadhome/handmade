<?php

/**
 * Cart_Model_Base_Cart
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $cart_id
 * @property integer $user_id
 * @property clob $session_data
 * @property integer $shippAddress_id
 * @property clob $mails
 * @property string $status
 * @property integer $whom_user_id
 * @property timestamp $date
 * @property integer $payment_id
 * @property integer $delivery_id
 * @property float $summ
 * @property integer $count
 * @property clob $additional
 * @property clob $commet_user
 * @property clob $commet_sell
 * @property User_Model_User $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Cart_Model_Base_Cart extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('Cart__Model__Carts');
        $this->hasColumn('cart_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('session_data', 'clob', 65535, array(
             'type' => 'clob',
             'length' => '65535',
             ));
        $this->hasColumn('shippAddress_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('mails', 'clob', 65535, array(
             'type' => 'clob',
             'length' => '65535',
             ));
        $this->hasColumn('status', 'string', 45, array(
             'type' => 'string',
             'length' => '45',
             ));
        $this->hasColumn('whom_user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('date', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('payment_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('delivery_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('summ', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('count', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('additional', 'clob', 65535, array(
             'type' => 'clob',
             'length' => '65535',
             ));
        $this->hasColumn('commet_user', 'clob', 65535, array(
             'type' => 'clob',
             'length' => '65535',
             ));
        $this->hasColumn('commet_sell', 'clob', 65535, array(
             'type' => 'clob',
             'length' => '65535',
             ));


        $this->index('C_user', array(
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
        $this->hasOne('User_Model_User as User', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));
    }
}