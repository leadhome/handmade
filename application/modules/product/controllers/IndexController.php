<?php
/**
 * @author Patsura Dmitiry <zaets28rus@gmail.com>
 * @data 2011.06.28
 */
class Product_IndexController
    extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout('my');
        $this->_helper->AjaxContext()->addActionContext('add', 'json')->initContext('json');
        $this->_helper->AjaxContext()->addActionContext('ajaxeditproductautocomplete', 'json')->initContext('json');
        $this->_helper->AjaxContext()->addActionContext('getcategories', 'json')->initContext('json');
    }
    public function indexAction()
    {

    }
	
	public function ajaxeditproductautocompleteAction() {
		$term=trim($this->_request->getParam('term'));
		if(!$term) return false;
		$type = $this->getRequest()->getParam('type');
		if($type=='material') {
			$rows = Product_Model_MaterialTable::getInstance()->getCompareMaterials($term);
		} else if($type=='tag') {
			$rows = Product_Model_TagTable::getInstance()->getCompareTags($term);
		} else {
			return false;
		}
		foreach ($rows as &$row) $result[]=$row['title'];
        echo $this->_helper->json($result);
		
	}
	
	
    
  
    
    public function getcategoriesAction() {
		$parent_id = (int) $this->_getParam('parent_id');
		if ($parent_id <= 0)
			throw new Zend_Controller_Action_Exception('parent_id must > 0');
		$this->view->categories = Product_Model_CategoryTable::getInstance()->getCategories($parent_id);;
    }
    
    public function addAction()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect("/user/index/login");
        $user = Zend_Auth::getInstance()->getIdentity();
        
        $userProductPhotos = new Zend_Session_Namespace('userProductPhotos');
        if($this->getRequest()->getPost())
        $userProductPhotos->type = 'add';
        if(!$this->getRequest()->isXmlHttpRequest() && !$this->getRequest()->isPost()){
                    $userProductPhotos->photos = array();
                    unset($userProductPhotos->PhotosDir);
        }
        $form = new Product_Form_Product();
        
        if (!$this->getRequest()->isXmlHttpRequest()) {
        $this->view->colors = Product_Model_Color::getMultiOptions();
        $this->view->tags = Product_Model_TagTable::getInstance()->getMyTagsArray($user->user_id);
        }
        if ( $this->getRequest()->isPost() ) {
            $post = $this->getRequest()->getPost();
            $form->populate($this->getRequest()->getPost());
            if($post['categories']) {
                $categories = Doctrine_Query::create()
                                    ->from('Product_Model_Category')
                                    ->where('parent_id = ?', $post['categories'])
                                    ->fetchArray();
                foreach($categories as $category){
                    $category2Array[$category['category_id']] = $category['title'];
                }
                $form->getElement('subCategories')->setMultiOptions($category2Array);
            }
            if ($form->isValid($post)) { 
                $this->view->error = 0;
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    $values = $form->getValues();
                    $product = new Product_Model_Product();
                    $product->category_id = $values['subCategories'];
                    $product->title = $values['title'];
                    $product->user_id = $user->user_id;
                    $product->description = $values['description'];
                    $product->production_time = $values['production_time'];
                    $product->size = $values['size'];
                    $product->price = $values['price'];
               
                    $photos = array();
                    $photos = $userProductPhotos->photos;
                    $product->photos = serialize($photos); 
                    
                    //tags
                    $product->Product__Model__TagProducts->Tag->title = unserialize($values['materials']);
                    //materials
                    $product->Product_Model_MaterialProduct->Material->title = 'dsfsd';
                    
                    $product->availlable_id = $values['availlable'];
                    $product->quantity = $values['quantity'];
                    $product->published = 1;
                    $product->save();
                    $productId = $product->get('product_id');
                    
                    // public/images/products/categoryId/user->user_id
                    $path = APPLICATION_PATH . '/../public/images/products/' . $product->category_id . '/' . $user->user_id;
                    // public/images/products/user_upload_dir
                    $userUploadDir = APPLICATION_PATH . '/../public/images/products/' . $userProductPhotos->PhotosDir;
                    if(!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    #chmod($path .'/*',0777);
                    exec('mv '.$userUploadDir.' '.$path);
                    $userProductPhotos->photos = array();
                    unset($userProductPhotos->PhotosDir);
                    die();
                    $this->_redirect('/');
                    
                }
            } else {
                    if ($this->getRequest()->isXmlHttpRequest()) {
                            $this->view->error = $form->getMessages();
                    } else {
                            $this->view->form = $form;
                    }
            }
       } else $this->view->form = $form;
    }
    
    public function editAction()
    {    
        if (!Zend_Auth::getInstance()->hasIdentity())
                throw new Exception('Вы не авторизорованый пользователь');
        $user = Zend_Auth::getInstance()->getIdentity();
        
        if( ($productId = $this->_getParam('productId', 0)) == 0)
                throw new Exception('Не указан индификатор продукта');
        $productId = (int) $productId;
              
        $userProductPhotos = new Zend_Session_Namespace('userProductPhotos');
        $userProductPhotos->type = 'edit';

        $product = Product_Model_ProductTable::getInstance()->findoneByproduct_id($productId);
        if(!$product)
            throw new Exception('По данному индефекатору не найдено не одного продукта');

        if($product->user_id != $user->user_id)
            throw new Exception('Вы не имеете прав на редактирование данного товара');

        $form = new Product_Form_EditProduct();
        $form->populate($product->toArray());
        $userProductPhotos->productId = $product;
        $this->view->colors = Product_Model_Color::getMultiOptions();
        $this->view->tags = Product_Model_TagTable::getInstance()->getMyTagsArray($user->user_id);  
        $this->view->form = $form;
        $this->view->product = $product;
        
        if ( $this->getRequest()->isPost() ) {
            $values = $form->getValues();
            
            if ($form->isValid($values)) {   
                $this->view->error = 0;
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    
                }
            } else {
                    if ($this->getRequest()->isXmlHttpRequest()) {
                            $this->view->error = $form->getMessages();       
                    }
            }
       }
    }
    
    public function publishAction()
    {
        $user = Zend_Auth::getInstance()->getIdentity();
        if (!$user) $this->_redirect("/");
        $productId = $this->_getParam('productId');
        if ((int)$productId == 0) $this->_redirect('/');
        $product = Product_Model_ProductTable::getInstance()
                                ->findonebyproduct_id($productId);
        if ($product->user_id != $user->user_id) $this->_redirect('/');
        if($product->published == 0) $product->published = 1;
        else $product->published = 0;
        $product->save();
        $this->_redirect($_SERVER['HTTP_REFERER']);
    }
}

function rec_copy ($from_path, $to_path) {
mkdir($to_path, 0777);
$this_path = getcwd();
if (is_dir($from_path)) {
chdir($from_path);
$handle=opendir('.');
while (($file = readdir($handle))!==false) {
if (($file != ".") && ($file != "..")) {
if (is_dir($file)) {
rec_copy ($from_path.$file."/", $to_path.$file."/");
chdir($from_path);
}
if (is_file($file)) copy($from_path.$file, $to_path.$file);
}
}
closedir($handle);
}
}