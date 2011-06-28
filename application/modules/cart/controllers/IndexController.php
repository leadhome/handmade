<?php

class Cart_IndexController extends Zend_Controller_Action {

    public function init() {
		$this->_helper->layout->setLayout('default');
		$this->_helper->AjaxContext()->addActionContext('addproducttocart', 'json')->initContext('json');
    }

    public function indexAction() {

    }
	//добавление продукта в корзину
	public function addproducttocartAction() {
		$product_ids = $this->getRequest()->product_id;
		
		if(!is_array($product_ids)) $product_ids=array(0=>$product_ids);
		else $product_ids = array_diff($product_ids, array(''));
		
		if(count($product_ids)==0 && !$this->getRequest()->isXmlHttpRequest()) {
			//редирект на 404
			$this->_redirect('404.html');
		} else if(count($product_ids)==0 && $this->getRequest()->isXmlHttpRequest()) {
			$this->view->error = 1;
			$this->view->message = 'Вы не выбрали товар';
		} else $this->view->error = 0;
		
		//проверка на существование продуктов и их статус
		$products = Product_Model_ProductTable::getInstance()->getProducts($product_ids);
		
		if(count($product_ids)==0  && !$this->getRequest()->isXmlHttpRequest()) {
			//редирект на 404
			$this->_redirect('404.html');
		} else if(count($product_ids)==0 && $this->getRequest()->isXmlHttpRequest()) {
			$this->view->error = 2;
			$this->view->message = 'Данный товар не существует или снят с публикации';
		} else $this->view->error = 0;
		
		$cart = new Zend_Session_Namespace('cart');
		
		foreach($products as $product) {
			$cart->products[$product['product_id']]->product_id = $product['product_id'];
			$cart->products[$product['product_id']]->title = $product['title'];
			$cart->products[$product['product_id']]->photos = unserialize($product['photos']);
			$cart->products[$product['product_id']]->author_id = $product['user_id'];
			$cart->products[$product['product_id']]->category_id = $product['category_id'];
			$cart->products[$product['product_id']]->single_price = $product['price'];
			$cart->products[$product['product_id']]->all_price += $product['price'];
			$cart->products[$product['product_id']]->counts++;
			$cart->total += $product['price'];
			$cart->counts++;
		}
		
		$this->view->total = $cart->total;
		$this->view->counts = $cart->counts;
		if(!$this->getRequest()->isXmlHttpRequest()) {
			$this->_redirect(base64_decode($this->getRequest()->return));
		}
	}
	//просмотр корзины
	public function showcartAction() {
		$this->view->headTitle('Корзина');
		$this->view->confirm = $this->getRequest()->confirm ? 1 : 0;
		$this->view->cart = new Zend_Session_Namespace('cart');
	}
	
}
