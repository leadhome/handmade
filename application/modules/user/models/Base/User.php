<?php

/**
 * User_Model_Base_User
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $user_id
 * @property string $email
 * @property string $password
 * @property string $firstname
 * @property string $lastname
 * @property string $midname
 * @property clob $about
 * @property integer $group_id
 * @property integer $tarif_id
 * @property integer $city_id
 * @property timestamp $date_expire
 * @property float $rating
 * @property float $summ
 * @property User_Model_Group $Group
 * @property User_Model_Tarif $Tarif
 * @property User_Model_City $City
 * @property Doctrine_Collection $User__Model__Messages
 * @property Doctrine_Collection $User__Model__Comments
 * @property Doctrine_Collection $User__Model__ShippAddresses
 * @property Doctrine_Collection $User__Model__Shops
 * @property Doctrine_Collection $Product__Model__Products
 * @property Doctrine_Collection $Product__Model__TagProducts
 * @property Doctrine_Collection $User__model__FavoriteAuthors
 * @property Doctrine_Collection $User__Model__FavoriteProducts
 * @property Doctrine_Collection $Banner__Model__Mains
 * @property Doctrine_Collection $User__Model__Operations
 * @property Doctrine_Collection $Cart__Model__Carts
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class User_Model_Base_User extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('User__Model__Users');
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('email', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('password', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('firstname', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('lastname', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('midname', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('about', 'clob', 65535, array(
             'type' => 'clob',
             'length' => '65535',
             ));
        $this->hasColumn('group_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('tarif_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('city_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('date_expire', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('rating', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('summ', 'float', null, array(
             'type' => 'float',
             ));


        $this->index('U_Group', array(
             'fields' => 
             array(
              0 => 'group_id',
             ),
             ));
        $this->index('U_Tarif', array(
             'fields' => 
             array(
              0 => 'tarif_id',
             ),
             ));
        $this->index('U_City', array(
             'fields' => 
             array(
              0 => 'city_id',
             ),
             ));
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'InnoDB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('User_Model_Group as Group', array(
             'local' => 'group_id',
             'foreign' => 'group_id'));

        $this->hasOne('User_Model_Tarif as Tarif', array(
             'local' => 'tarif_id',
             'foreign' => 'tarif_id'));

        $this->hasOne('User_Model_City as City', array(
             'local' => 'city_id',
             'foreign' => 'city_id'));

        $this->hasMany('User_Model_Message as User__Model__Messages', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasMany('User_Model_Comment as User__Model__Comments', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasMany('User_Model_ShippAddress as User__Model__ShippAddresses', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasMany('User_Model_Shop as User__Model__Shops', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasMany('Product_Model_Product as Product__Model__Products', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasMany('Product_Model_TagProduct as Product__Model__TagProducts', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasMany('User_Model_FavoriteAuthor as User__model__FavoriteAuthors', array(
             'local' => 'user_id',
             'foreign' => 'author_user_id'));

        $this->hasMany('User_Model_FavoriteProduct as User__Model__FavoriteProducts', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasMany('Banner_Model_Main as Banner__Model__Mains', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasMany('User_Model_Operation as User__Model__Operations', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasMany('Cart_Model_Cart as Cart__Model__Carts', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));
    }
}