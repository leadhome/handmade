<?php

/**
 * User_Model_Base_Operation
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $operation_id
 * @property integer $user_id
 * @property timestamp $pay_date_start
 * @property timestamp $pay_date_end
 * @property string $SignatureValue
 * @property float $summ
 * @property integer $status
 * @property User_Model_User $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class User_Model_Base_Operation extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('User__Model__Operations');
        $this->hasColumn('operation_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('pay_date_start', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('pay_date_end', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('SignatureValue', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('summ', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('status', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));


        $this->index('O_user', array(
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