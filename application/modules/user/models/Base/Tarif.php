<?php

/**
 * User_Model_Base_Tarif
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $tarif_id
 * @property string $title
 * @property string $description
 * @property integer $price
 * @property integer $product_limit
 * @property Doctrine_Collection $User__Model__Users
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class User_Model_Base_Tarif extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('User__Model__Tarifs');
        $this->hasColumn('tarif_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('description', 'string', 45, array(
             'type' => 'string',
             'length' => '45',
             ));
        $this->hasColumn('price', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('product_limit', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));

        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'InnoDB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('User_Model_User as User__Model__Users', array(
             'local' => 'tarif_id',
             'foreign' => 'tarif_id'));
    }
}