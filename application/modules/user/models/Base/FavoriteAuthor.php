<?php

/**
 * User_Model_Base_FavoriteAuthor
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $favoriteAuthor_id
 * @property integer $user_id
 * @property integer $author_user_id
 * @property User_Model_User $AuthorUser
 * @property User_Model_User $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class User_Model_Base_FavoriteAuthor extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('User__model__FavoriteAuthor');
        $this->hasColumn('favoriteAuthor_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('author_user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));


        $this->index('FA_Author_User', array(
             'fields' => 
             array(
              0 => 'author_user_id',
             ),
             ));
        $this->index('FA_User', array(
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
        $this->hasOne('User_Model_User as AuthorUser', array(
             'local' => 'author_user_id',
             'foreign' => 'user_id'));

        $this->hasOne('User_Model_User as User', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));
    }
}