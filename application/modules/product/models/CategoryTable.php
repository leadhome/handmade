<?php

/**
 * Product_Model_CategoryTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Product_Model_CategoryTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object Product_Model_CategoryTable
     */
    public static function getInstance() {
        return Doctrine_Core::getTable('Product_Model_Category');
    }
	public function getCategories($parent_id=false) {
		if($parent_id===false) $rows = $this->createQuery()->orderBy('parent_id ASC')->execute();
		else {
			if($parent_id==0) $rows = $this->findByDql('parent_id IS NULL')->toKeyValueArray('category_id', 'title');
			else $rows = $this->findByDql('parent_id = ?',$parent_id)->toKeyValueArray('category_id', 'title');
		}
		return $rows;
	}
}