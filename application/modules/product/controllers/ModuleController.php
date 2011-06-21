<?php

class Product_ModuleController extends Zend_Controller_Action {

    public function init() {

    }

    public function indexAction() {

    }
	public function menuAction() {
		$this->view->categories = Product_Model_CategoryTable::getInstance()->getCategories()->toArray();
    }
}
