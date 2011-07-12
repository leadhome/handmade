<?php
/**
 * @author Patsura Dmitiry <zaets28rus@gmail.com>
 * @data 2011.06.28
 */
class Product_IndexController
    extends Zend_Controller_Action
{
    /**
     * 
     */
    public function init()
    {
        $this->_helper->layout->setLayout('my');
        $this->_helper->AjaxContext()->addActionContext('add', 'json')->initContext('json');
        $this->_helper->AjaxContext()->addActionContext('ajaxeditproductautocomplete', 'json')->initContext('json');
        $this->_helper->AjaxContext()->addActionContext('getcategories', 'json')->initContext('json');
    }
    
    /**
     * 
     */
    public function indexAction()
    {

    }
	
    /**
     *
     * @return type 
     */
    public function ajaxeditproductautocompleteAction()
    {
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
    
    /**
     * 
     */
    public function getcategoriesAction()
    {
        $parent_id = (int) $this->_getParam('parent_id');
        if ($parent_id <= 0)
            throw new Zend_Controller_Action_Exception('parent_id must > 0');
        $this->view->categories = Product_Model_CategoryTable::getInstance()->getCategories($parent_id);;
    }
    
    /**
     * 
     */
    public function addAction()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect("/user/index/login");
        $user = Zend_Auth::getInstance()->getIdentity();
             
        $session = new Zend_Session_Namespace('userProductPhotos');
        if(!$this->getRequest()->isXmlHttpRequest() && !$this->getRequest()->isPost()){
            $session->type = 'add';
            $session->photos = array();
            unset($session->PhotosDir);
        }
        $form = new Product_Form_Add();
        
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
                    $date_year = date('Y');
                    $date_month = date('m');
                    $date_day = date('d');
                    $values = $form->getValues();
                    $product = new Product_Model_Product();
                    $product->category_id = $values['subCategories'];
					$product->date_created = $date_year.'-'.$date_month.'-'.$date_day.' '.date('H:s:i');
                    $product->title = $values['title'];
                    $product->user_id = $user->user_id;
                    $product->description = $values['description'];
                    $product->production_time = $values['production_time'];
                    $product->size = $values['size'];
                    $product->price = $values['price'];
               
                    $photos = array();
                    $photos = $session->photos;
                    $product->photos = serialize($photos); 
                    
                    $product->availlable_id = $values['availlable_id'];
					if($product->availlable_id==1) $product->quantity = $values['quantity'];
                    $product->published = 1;
                    $product->save();
                    $productId = $product->get('product_id');
                    
                    //save colors
                    $colors = array_unique($post['color']);
                    $colors = array_intersect(array_keys($this->view->colors),$colors);
                    array_splice($colors,3);
                    foreach($colors as $color){
                        $model = new Product_Model_ColorProduct();
                        $model->product_id = $productId;
                        $model->color_id = $color;
                        $model->save();
                    }
					
                    //save materials
                    $materials = unserialize($values['materials']);
                    foreach($materials as $material){
                        $material_exmp = Product_Model_MaterialTable::getInstance()->findOneBy('title', $material);
                        if($material_exmp) {
                            $productMaterial = new Product_Model_MaterialProduct;
                            $productMaterial->product_id = $productId;
                            $productMaterial->material_id = $material_exmp->material_id;
                            $productMaterial->save();
                        } else {
                            $materials_m = new Product_Model_Material();
                            $materials_m->title = $material;
                            $materials_m->save();
                            $materialId = $materials_m->get('material_id');
                            
                            $productMaterial = new Product_Model_MaterialProduct;
                            $productMaterial->product_id = $productId;
                            $productMaterial->material_id = $materialId;
                            $productMaterial->save();
                        }
                    }
                    
                    //save tags
                    $tags = unserialize($values['tags']);                
                    foreach ($tags as &$value) {
                        $tag_exmp = Product_Model_TagTable::getInstance()->findOneBy('title', $value);
                        if($tag_exmp) {
                            $model = new Product_Model_TagProduct();
                            $model->tag_id = $tag_exmp->tag_id;
                            $model->user_id = $user->user_id;
                            $model->product_id = $productId;
                            $model->save();
                        } else {
                            $tag = new Product_Model_Tag();
                            $tag->title = $value;
                            $tag->save();
                            $tagId = $tag->get('tag_id');
                            
                            $model = new Product_Model_TagProduct();
                            $model->tag_id = $tagId;
                            $model->user_id = $user->user_id;
                            $model->product_id = $productId;
                            $model->save();
                        }
                    }

                    // public/images/products/categoryId/user->user_id
                    $path = APPLICATION_PATH . '/../public/images/products/' . $date_year . '/' . $date_month.'/'.$date_day.'/'.$user->user_id;
                    // public/images/products/user_upload_dir
                    $userUploadDir = APPLICATION_PATH . '/../public/cache/' . $session->PhotosDir;
                    if(!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    exec('mv '.$userUploadDir.'/* '.$path.'/');
                    exec('rm -rf '.$userUploadDir);
                    $session->photos = array();
                    unset($session->PhotosDir);
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
        if (!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect("/user/index/login");
        $user = Zend_Auth::getInstance()->getIdentity();
        
        if( ($productId = $this->_getParam('productId', 0)) == 0)
                throw new Exception('Не указан индификатор продукта');
        $productId = (int) $productId;
              
        $product = Product_Model_ProductTable::getInstance()->findoneByproduct_id($productId);
        if(!$product)
            throw new Exception('По данному индефекатору не найдено не одного продукта');

        if($product->user_id != $user->user_id)
            throw new Exception('Вы не имеете прав на редактирование данного товара');
        
        $session = new Zend_Session_Namespace('userProductPhotos');
        if(!$this->getRequest()->isXmlHttpRequest() && !$this->getRequest()->isPost()){
            $session->photos = array();
            unset($session->PhotosDir);
            $session->type = 'edit';
            $session->productId = $productId;
            $session->date_created = $product->date_created;
        }
        $form = new Product_Form_Edit();
        
        if ( $this->getRequest()->isPost() ) {
            $post = $this->getRequest()->getPost();
            $form->populate($post);
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
					$date_year = date('Y');
					$date_month = date('m');
					$date_day = date('d');
                    $values = $form->getValues();
                    $product = new Product_Model_Product();
                    $product->category_id = $values['subCategories'];
					$product->date_created = $date_year.'-'.$date_month.'-'.$date_day.' '.date('H:s:i');
                    $product->title = $values['title'];
                    $product->user_id = $user->user_id;
                    $product->description = $values['description'];
                    $product->production_time = $values['production_time'];
                    $product->size = $values['size'];
                    $product->price = $values['price'];
               
                    $photos = array();
                    $photos = $session->photos;
                    $product->photos = serialize($photos); 
                    
                    $product->availlable_id = $values['availlable_id'];
                    if($product->availlable_id==1) $product->quantity = $values['quantity'];
                    
                    $product->published = 1;
                    $product->save();
                    $productId = $product->get('product_id');
                    
					//save colors
					$colors = array_unique($post['color']);
					$colors = array_intersect(array_keys($this->view->colors),$colors);
					array_splice($colors,3);
                    foreach($colors as $color){
						$model = new Product_Model_ColorProduct();
						$model->product_id = $productId;
						$model->color_id = $color;
						$model->save();
                    }
					
                    //save materials
                    $materials = unserialize($values['materials']);
                    foreach($materials as $material){
                        $material_exmp = Product_Model_MaterialTable::getInstance()->findOneBy('title', $material);
                        if($material_exmp) {
                            $productMaterial = new Product_Model_MaterialProduct;
                            $productMaterial->product_id = $productId;
                            $productMaterial->material_id = $material_exmp->material_id;
                            $productMaterial->save();
                        } else {
                            $materials_m = new Product_Model_Material();
                            $materials_m->title = $material;
                            $materials_m->save();
                            $materialId = $materials_m->get('material_id');
                            
                            $productMaterial = new Product_Model_MaterialProduct;
                            $productMaterial->product_id = $productId;
                            $productMaterial->material_id = $materialId;
                            $productMaterial->save();
                        }
                    }
                    
                    //save tags
                    $tags = unserialize($values['tags']);                
                    foreach ($tags as &$value) {
                        $tag_exmp = Product_Model_TagTable::getInstance()->findOneBy('title', $value);
                        if($tag_exmp) {
                            $model = new Product_Model_TagProduct();
                            $model->tag_id = $tag_exmp->tag_id;
                            $model->user_id = $user->user_id;
                            $model->product_id = $productId;
                            $model->save();
                        } else {
                            $tag = new Product_Model_Tag();
                            $tag->title = $value;
                            $tag->save();
                            $tagId = $tag->get('tag_id');
                            
                            $model = new Product_Model_TagProduct();
                            $model->tag_id = $tagId;
                            $model->user_id = $user->user_id;
                            $model->product_id = $productId;
                            $model->save();
                        }
                    }

                    // public/images/products/categoryId/user->user_id
                    $path = APPLICATION_PATH . '/../public/images/products/' . $date_year . '/' . $date_month.'/'.$date_day.'/'.$user->user_id;
                    // public/images/products/user_upload_dir
                    $userUploadDir = APPLICATION_PATH . '/../public/cache/' . $session->PhotosDir;
                    if(!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
					echo 'mv '.$userUploadDir.' '.$path.'/';
					// echo 'rm -rf '.mb_substr($userUploadDir,0,-2);
                    exec('mv '.$userUploadDir.'/* '.$path.'/');
                    exec('rm -rf '.$userUploadDir);
					die();
					// die('rm -rf '.mb_substr($userUploadDir,0,-2));
                    // exec('mv '.$path.'/'.$session->PhotosDir.' '.$path.'/'.$user->user_id); 
                    $session->photos = array();
                    unset($session->PhotosDir);
                    $this->_redirect('/');
                    
                }
            } else {
                    if ($this->getRequest()->isXmlHttpRequest()) {
                            $this->view->error = $form->getMessages();
                    } else {
                            $form->populate($post);
                            $this->view->form = $form;
                    }
            }
       } else {
        $this->view->product = $product;
        $this->view->colors = Product_Model_Color::getMultiOptions();
        $this->view->tags = Product_Model_TagTable::getInstance()->getTagsByProductId($product->product_id);
        $this->view->materials = Product_Model_MaterialTable::getInstance()->getMaterialsByProductId($product->product_id);
        $this->view->user_tags = Product_Model_TagTable::getInstance()->getMyTagsArray($user->user_id);
            
        $result = array();
        foreach ($this->view->materials as &$row) $result[] = $row['title'];
        $this->view->materialsJSON = $result;
        
        $result = array();
        foreach ($this->view->tags as &$row) $result[] = $row['title'];
        $this->view->tagsJSON = $result;
        
        $subCategories = $form->getElement('subCategories');
        $category_parent_id = Product_Model_CategoryTable::getInstance()->findOneBy('category_id', $product->category_id)->parent_id;
        $categoriesArray = Product_Model_CategoryTable::getInstance()->getCategories($category_parent_id);
        $subCategories->setMultiOptions($categoriesArray)
                      ->setValue($product->category_id);
        $form->getElement('categories')->setValue($category_parent_id);
        $form->populate($product->toArray());
        $this->view->form = $form;
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