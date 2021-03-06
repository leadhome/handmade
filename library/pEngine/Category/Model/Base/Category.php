<?php

/**
 * pEngine_Category_Model_Base_Category
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property string $title
 * @property text $description
 * @property text $uri
 * @property boolean $target
 * @property integer $order
 * @property boolean $publish
 * @property Doctrine_Collection $Articles
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class pEngine_Category_Model_Category extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('pengine__model__category');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('lft', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('rgt', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('level', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('name', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '64',
             ));
        $this->hasColumn('title', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '64',
             ));
        $this->hasColumn('description', 'text', null, array(
             'type' => 'text',
             ));
        $this->hasColumn('uri', 'text', null, array(
             'type' => 'text',
             ));
        $this->hasColumn('target', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('order', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('publish', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => 1,
             ));

        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'InnoDB');
    }

    public function setUp()
    {
        parent::setUp();

        $nestedset0 = new Doctrine_Template_NestedSet();
        $this->actAs($nestedset0);
    }
}
