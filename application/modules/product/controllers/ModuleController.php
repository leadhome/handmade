<?php

class Product_ModuleController extends Zend_Controller_Action {

    public function init() {

    }

    public function indexAction() {

    }
	public function menuAction() {
		$this->view->categories = Product_Model_CategoryTable::getInstance()->getCategories()->toArray();
    }
	public function editorschoiceAction() {
		// $this->view->products = Product_Model_EditorsChoiceTable::getInstance()->getProducts()->toArray();
		// print_r($this->products);
	}	
}

/*
TRASH

// $categories = Product_Model_CategoryTable::getInstance()->getRootCategories();
		$categories = Product_Model_CategoryTable::getInstance()->getRootCategories();
		// print_r($categories->toArray());
		foreach($categories as $row) {
			print_r($row->parents->toArray());
		}
		
		// print_r($categories->parents);
		
		
		
		
		
		
		
		
		die('dsdsd');
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		// $categories = $categories->C_Category;
		// foreach($categories as $category) {
			// echo '<pre>';
				 // $this->printRTree($category);
			// $category->getNormalIterator());
		 // print_r(get_class_methods($category));
		 // print_r($category->getNode());
		 // echo '</pre>';
		// }
		
		// echo '<pre>';
		// $this->printRTree($categories);
		// print_r(get_class_methods($categories));
		// print_r($categories);
		// echo '</pre>';
		// die();
		// print_r($categories->toArray());
		// $categories = $categories->toArray();
		// $categories = $categories[0];
		// echo $categories->getName();
		// print_r($categories->Children->getIterator());
	}
	
	// public function printRTree($node, $level = 0) {
		// echo str_repeat( "\t", $level) . "1" . "\n"; 
	
		// if (($children = $node->Children->getIterator()) && $children->count()>0) {
			// $level++;
			// while (($child = $children->current())) {
				// $this->printRTree( $child, $level);
				// $children->next();
			// }
		// }
	// }
	
    public function editorschoiceAction() {
	
		// $products = Product_Model_CategoryTable::getInstance()->findAll();
		// $relations = $products->getTable()->hasRelation('Product__Model__Categories');
		// print_r($relations);
		// print_r($relations->toArray());
		// $relations = $products->getTable()->getRelations();
		// foreach($relations as $key=>$row)
   // {
    // echo "<pre>"; var_dump($key); echo "</pre>"; 


   // }
// Product__Model__Categories
		// $products = Doctrine_Query::create()
			// ->from('Product_Model_Category')
			// ->where('parent IS NULL')
			// ->execute();
		// print_r($products->toArray());
		// foreach($products as $row) {
		
		// }
		
		$products = Doctrine_Core::getTable('Product_Model_Category')->findByDql( 'WHERE parent IS NULL');
	
		print_r($products->Children->getIterator());
	
	
	
	
		// $children = $products->Children->getIterator()
	
	
	
	
		// $products = Product_Model_CategoryTable::getInstance()->findAll();
		// $products->getTable()->hasRelation('C_Category');
		// try {
		// $products = Product_Model_CategoryTable::getInstance()->getRecordCategory(2);
		// print_r($products->toArray());
		// } catch(exption $e){
			// print_r($e);
			// exit();
		// }

		die('sdsds');
		
		
		
		
		
		
		
		
		
		
		//$products = Product_Model_CategoryTable::getInstance();
		// $categories = Doctrine_Core::getTable('Product_Model_Categories');
		// $categories = $categories->findOneById(28);
		// $products =  Doctrine_Core::getTable('Product_Model_Category')->getProduct_Model_CategoryRecord(28);
		// $products=$products->toArray();
		// $products = Product_Model_CategoryTable::getInstance()->findOneById("1");
		// $products = $products->find(1);
		// $this->view->show_head = true;
		
		// die();
        // $auth = Zend_Auth::getInstance();
        // if (Inc_Auth_User::getInstance()->hasAdmin()) {
            // $identity = $auth->getIdentity();
            // $this->view->my_name = $identity->name;
            // $identity = $auth->getIdentity();
            // if ($identity->Group->title != 'Administrator')
                // $this->view->show_head = false;
            // else
                // $this->view->show_head = true;
        // }
        // else
            // $this->view->show_head = false;


*/