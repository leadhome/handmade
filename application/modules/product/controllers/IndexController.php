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
		// eb7007dd03fc847cb028d6279cf73318
		// $category_id = 75;
		// $user_id = 1;
		// $productId = 8;

		// $pathtoimages = '/home/lime/public_html/handmade/public/images/products/' . $category_id . '/' . $user_id . '/' . $productId;
		// if(!is_dir($pathtoimages)) {
			// mkdir($pathtoimages, 0777, true);
			// chmod('/home/lime/public_html/handmade/public/images/products/' . $product->category_id . '/',0777);
			// chmod('/home/lime/public_html/handmade/public/images/products/' . $product->category_id . '/'. $user->user_id . '/',0777);
			// chmod('/home/lime/public_html/handmade/public/images/products/' . $product->category_id . '/'. $user->user_id . '/'. $productId,0777);
		// }
		
		// $userUploadDir = '/home/lime/public_html/handmade/public/images/products/eb7007dd03fc847cb028d6279cf73318/*';
		// $output = exec('mv '.$userUploadDir.' '.$pathtoimages); 
		
		// die();
	
	
	
	
		// chmod('/home/lime/public_html/handmade/public/images/products/29/*',0777);
		// echo $output = exec('mv /home/lime/public_html/handmade/public/images/products/8d90edf85af2558ebe767e594ea337ef /home/lime/public_html/handmade/public/images/products/71/1/13'); 
		// echo '<pre>';
			// print_r($output);
		// echo '</pre>';
		// die();
        if (!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect("/user/index/login");
        $user = Zend_Auth::getInstance()->getIdentity();
        
        $userProductPhotos = new Zend_Session_Namespace('userProductPhotos');
        $userProductPhotos->type = 'add';
        #Zend_Debug::dump($userProductPhotos->photos);
        
        $form = new Product_Form_Product();
        
        if (!$this->getRequest()->isXmlHttpRequest()) {
        $this->view->colors = Product_Model_Color::getMultiOptions();
        $this->view->tags = Product_Model_TagTable::getInstance()->getMyTagsArray($user->user_id);
        }
        if ( $this->getRequest()->isPost() ) {
            $values = $form->getValues();
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
                    $product = new Product_Model_Product();
                    $product->category_id = $post['subCategories'];
                    // $product->title = $post['title'];
                    // $product->user_id = $user->user_id;
                    // $product->description = $post['description'];
                    // $product->production_time = $post['production_time'];
                    // $product->size = $post['size'];
                    // $product->price = $post['price'];
                    
                    // $photos = array();
                    // $photos = $userProductPhotos->photos;
                    // $product->photos = serialize($photos); 
                            
                    // $product->availlable_id = $post['availlable'];
                    // $product->quantity = $post['quantity'];
                    // $product->published = 1;
                    $product->save();
                    
                    $productId = $product->get('product_id');
                    // $pathtoimages = APPLICATION_PATH . '/../public/images/products/' . $product->category_id . '/' . $user->user_id . '/' . $productId;
                    $pathtoimages = '/home/lime/public_html/handmade/public/images/products/' . $product->category_id . '/' . $user->user_id . '/' . $productId;
                    if(!is_dir($pathtoimages)) {
                        mkdir($pathtoimages, 0777, true);
						// chmod('/home/lime/public_html/handmade/public/images/products/' . $product->category_id . '/',0777);
						// chmod('/home/lime/public_html/handmade/public/images/products/' . $product->category_id . '/'. $user->user_id . '/',0777);
						// chmod('/home/lime/public_html/handmade/public/images/products/' . $product->category_id . '/'. $user->user_id . '/'. $productId,0777);
						exec('chmod 0777 -R /home/lime/public_html/handmade/public/images/products/' . $product->category_id . '/');
                    }
					
                    // $userUploadDir = APPLICATION_PATH . '/../public/images/products/' . $userProductPhotos->PhotosDir;
                    $userUploadDir = '/home/lime/public_html/handmade/public/images/products/' . $userProductPhotos->PhotosDir.'/*';
                    $output = exec('mv '.$userUploadDir.' '.$pathtoimages); 
				
                    echo 'mv '.$userUploadDir.' '.$pathtoimages;
                    $userProductPhotos->photos == array();
                    unset($userProductPhotos->PhotosDir);
                    #rmdir($userUploadDir);
                    die($userUploadDir);
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