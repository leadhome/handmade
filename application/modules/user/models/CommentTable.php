<?php

/**
 * User_Model_CommentTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class User_Model_CommentTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object User_Model_CommentTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('User_Model_Comment');
    }
}