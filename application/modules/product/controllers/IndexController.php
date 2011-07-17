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
        $this->_helper->layout->setLayout('default');
        $this->_helper->AjaxContext()->addActionContext('add', 'json')->initContext('json');
        $this->_helper->AjaxContext()->addActionContext('ajaxeditproductautocomplete', 'json')->initContext('json');
        $this->_helper->AjaxContext()->addActionContext('getcategories', 'json')->initContext('json');
        $this->_helper->AjaxContext()->addActionContext('edit', 'json')->initContext('json');
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
        $this->view->categories = Product_Model_CategoryTable::getInstance()->getCategories($parent_id);
    }
    
    /**
     * Добавление товара
     */
    public function addAction() {
		//проверка на авторизацию пользователя
        if(!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect("/user/index/login");
		
		//данные о пользователе
        $user = Zend_Auth::getInstance()->getIdentity();

        //проверка на существование у пользователя магазина
        $shop = User_Model_ShopTable::getInstance()->findOneByUser_id($user->user_id);
        if(!$shop) $this->_redirect("/user/shop/create");
		
             
        $session = new Zend_Session_Namespace('userProductPhotos');
        if(!$this->getRequest()->isXmlHttpRequest() && !$this->getRequest()->isPost()){
            if($session->PhotosDir) {
                $userUploadDir = APPLICATION_PATH . '/../public/cache/' . $session->PhotosDir;
                exec('rm -rf '.$userUploadDir);
            }
            $session->type = 'add';
            $session->photos = array();
			unset($session->PhotosDir);
        }
		
        $form = new Product_Form_AddProduct();
        
        if(!$this->getRequest()->isXmlHttpRequest()) {
			$this->view->colors = Product_Model_Color::getMultiOptions();
			$this->view->tags = Product_Model_TagTable::getInstance()->getMyTagsArray($user->user_id);
        }
		
        if( $this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $form->populate($this->getRequest()->getPost());
            if($post['categories']) {
				$form->getElement('subCategories')->setMultiOptions(Product_Model_CategoryTable::getInstance()->getCategories($post['categories']));
            }
			
            if ($form->isValid($post)) { 
                $this->view->error = 0;
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    //подготовка данных
                    $date_year = date('Y');
                    $date_month = date('m');
                    $date_day = date('d');
                    $values = $form->getValues();

                    //фотографии
                    $new_photos = array();
                    $photos = unserialize(stripslashes($values['photos']));					
                    if(count($session->photos)>0 && count($photos)>0) {
                            $i = 0;
                            foreach($session->photos as $photo) {
                                    foreach($photos['lists'] as $value) {
                                            if($value['name']==$photo) {
                                                    if($photos['main']==$photo) {
                                                            $new_photos['main'] = $photo;
                                                    }
                                                    $new_photos['lists'][$i]['name'] = $value['name'];
                                                    $new_photos['lists'][$i]['desc'] = $value['desc'];
                                                    $i++;
                                            }
                                    }	
                            }
                            $new_photos['dir_date'] = array( $date_year,$date_month,$date_day);
                            if($new_photos['main']=='') $new_photos['main'] = $new_photos['lists'][0]['name'];
                    }
                    //материалы
                    $materials = unserialize(stripslashes($values['materials']));
                    //тэги
                    $tags = unserialize(stripslashes($values['tags'])); 
                    //

                    //сохранение
                    $product = Product_Model_ProductTable::getInstance()->getRecord();	
                    $product->date_created = $date_year.'-'.$date_month.'-'.$date_day.' '.date('H:s:i');
                    $product->category_id = $values['subCategories'];
                    $product->title = $values['title'];
                    $product->user_id = $user->user_id;
                    $product->description = $values['description'];
                    $product->production_time = $values['production_time'];
                    $product->size = $values['size'];
                    $product->price = $values['price'];
                    $product->photos = serialize($new_photos);
                    $product->published = 1;
                    $product->shop_id = $shop->shop_id;
                    $product->availlable_id = $values['availlable_id'];
                    if($product->availlable_id==1) $product->quantity = $values['quantity'];
                    //Цвет
                    if(count($post['color'])>0) {
                            $colors = array_unique($post['color']);
                            $colors = array_intersect(array_keys($this->view->colors),$colors);
                            array_splice($colors,3);
                            foreach($colors as $key=>$color) {							
                                    $product->ColorProduct[$key]->color_id = $color;
                            }					
                    }
                    if(count($materials)>0) {
                            foreach($materials as $key=>$material){
                                    $material_search = Product_Model_MaterialTable::getInstance()->findOneByTitle($material);
                                    if($material_search) {
                                            $product->MaterialProduct[$key]->material_id = $material_search->material_id;
                                    } else {
                                            $product->MaterialProduct[$key]->Material->title = $material;
                                    }
                            }
                    }
                    if(count($tags)>0) {
                            foreach($tags as $key=>$tag){
                                    $tag_search = Product_Model_TagTable::getInstance()->findOneByTitle($tag);
                                    if($tag_search) {
                                            $product->TagProduct[$key]->tag_id = $tag_search->tag_id;
                                            $product->TagProduct[$key]->user_id = $user->user_id;
                                    } else {
                                            $product->TagProduct[$key]->Tag->title = $tag;
                                            $product->TagProduct[$key]->user_id = $user->user_id;
                                    }
                            }
                    }
                    $product->save();
                    //

                    //фотографии
                    //public/images/products/year/month/day/user_id/product_id
                    $path = APPLICATION_PATH . '/../public/images/products/' . $date_year . '/' . $date_month.'/'.$date_day.'/'.$user->user_id.'/'.$product->product_id;
                    //public/images/cache/user_upload_dir
                    $userUploadDir = APPLICATION_PATH . '/../public/cache/' . $session->PhotosDir;
                    if(!is_dir($path)) mkdir($path, 0777, true);
                    //перенос изображений
                    exec('mv '.$userUploadDir.'/* '.$path.'/');
                    //удаление кэша
                    exec('rm -rf '.$userUploadDir);
					//очистка сессии
					$session->photos = array();
                    unset($session->PhotosDir);
					//
					
                    $this->_redirect('/');  
                }
            } else {
				if ($this->getRequest()->isXmlHttpRequest()) $this->view->error = $form->getMessages();
				else $this->view->form = $form;
            }
       } else $this->view->form = $form;
    }
    
    public function editAction()
    {    
        if (!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect("/user/index/login");
        $user = Zend_Auth::getInstance()->getIdentity();
        $shop = User_Model_ShopTable::getInstance()->findOneByUser_id($user->user_id);
        if(!$shop) $this->_redirect("/user/shop/create");
        
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
            $session->photosInfo = unserialize($product->photos);
            $session->date_created = $product->date_created;
        }
		// Zend_Debug::dump($session->photosInfo );
		// die();
        $form = new Product_Form_EditProduct();
        
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
					
                    $new_photos = array();
                    $photos = unserialize(stripslashes($values['photos']));
                    if(count($session->photos)>0 && count($photos)>0) {
                            $i = 0;
                            foreach($session->photos as $photo) {
                                    foreach($photos['lists'] as $value) {
                                            if($value['name']==$photo) {
                                                    if($photos['main']==$photo) {
                                                            $new_photos['main'] = $photo;
                                                    }
                                                    $new_photos['lists'][$i]['name'] = $value['name'];
                                                    $new_photos['lists'][$i]['desc'] = $value['desc'];
                                                    $i++;
                                            }
                                    }	
                            }
                            $new_photos['dir_date'] = array( $date_year,$date_month,$date_day);
                            if($new_photos['main']=='') $new_photos['main'] = $new_photos['lists'][0]['name'];
                    }
					
                    $product->category_id = $values['subCategories'];
                    $product->date_created = $date_year.'-'.$date_month.'-'.$date_day.' '.date('H:s:i');
                    $product->title = $values['title'];
                    $product->user_id = $user->user_id;
                    $product->description = $values['description'];
                    $product->production_time = $values['production_time'];
                    $product->size = $values['size'];
                    $product->price = $values['price'];
               
                    $product->photos = serialize($new_photos); 
                    
                    $product->availlable_id = $values['availlable_id'];
                    if($product->availlable_id==1) $product->quantity = $values['quantity'];
                    
                    $product->published = 1;
                    $product->save();
                    $productId = $product->get('product_id');
                    
                    //save colors
                    $colorsMultiOptions = Product_Model_Color::getMultiOptions();
                    $colorsProduct = Product_Model_ColorProductTable::getInstance()->findBy('product_id', $productId);
                    $colorsProduct->delete();
                    //save colors
                    $colors = array_unique($post['color']);
                    $colors = array_intersect(array_keys($colorsMultiOptions), $colors);
                    array_splice($colors, 3);
                    foreach($colors as $color){
                        if($color > 0){
                            $model = new Product_Model_ColorProduct();
                            $model->product_id = $productId;
                            $model->color_id = $color;
                            $model->save();
                        }
                    }

                    //save materials
                    $materials = unserialize($values['materials']);
                    $materialsProducts = Product_Model_MaterialProductTable::getInstance()->findBy('product_id', $productId);
                    $do = false;
                    if(count($materials) > 0) {
                        foreach($materials as $material){
                            $do = false;
                            if(strlen($material) > 0) {
                                $material_exmp = Product_Model_MaterialTable::getInstance()->findOneBy('title', $material);
                                if($material_exmp) {
                                    if(count($materialsProducts) > 0) {
                                        foreach($materialsProducts as $key => $materialProduct){
                                            if ($materialProduct->material_id == $material_exmp->material_id) {
                                                unset($materialsProducts[$key]);
                                                $do = true;
                                                continue;
                                            }
                                        }   
                                        if(!$do) {
                                            $productMaterial = new Product_Model_MaterialProduct;
                                            $productMaterial->product_id = $productId;
                                            $productMaterial->material_id = $material_exmp->material_id;
                                            $productMaterial->save();
                                        } 
                                    } else {
                                        $productMaterial = new Product_Model_MaterialProduct;
                                        $productMaterial->product_id = $productId;
                                        $productMaterial->material_id = $material_exmp->material_id;
                                        $productMaterial->save();
                                    }
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
                        }
                    }
                    $materialsProducts->delete();

                    //save tags
                    $tags = unserialize(stripslashes($values['tags']));  
                    $tagsProducts = Product_Model_TagProductTable::getInstance()->findBy('product_id', $productId);
                    $do = false;
                    if(count($tags > 0)) {
                        foreach($tags as $tag){
                            $do = false;
                            $tag_exmp = Product_Model_TagTable::getInstance()->findOneBy('title', $tag);
                            if($tag_exmp) {
                                if(count($tagsProducts) > 0) {
                                    foreach($tagsProducts as $key => $tagProduct){
                                        if ($tagProduct->tag_id == $tag_exmp->tag_id) {
                                            unset($tagsProducts[$key]);
                                            $do = true;
                                            continue;
                                        }
                                    }
                                    if(!$do) {
                                        $model = new Product_Model_TagProduct();
                                        $model->tag_id = $tag_exmp->tag_id;
                                        $model->user_id = $user->user_id;
                                        $model->product_id = $productId;
                                        $model->save();
                                    } 
                                } else {
                                    $model = new Product_Model_TagProduct();
                                    $model->tag_id = $tag_exmp->tag_id;
                                    $model->user_id = $user->user_id;
                                    $model->product_id = $productId;
                                    $model->save();
                                }
                            } else {
                                $tag_m = new Product_Model_Tag();
                                $tag_m->title = $tag;
                                $tag_m->save();
                                $tagId = $tag_m->get('tag_id');

                                $model = new Product_Model_TagProduct();
                                $model->tag_id = $tagId;
                                $model->user_id = $user->user_id;
                                $model->product_id = $productId;
                                $model->save();
                            }
                        }
                    }
                    $tagsProducts->delete();
                    die();
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
        $photos = unserialize($product->photos);
        $new_photos = array();
        $new_photos['main'] = $photos['main'];
        if ( count($photos) > 0 ) {
            foreach($photos['lists'] as $photo) {
                    $new_photos['lists'][] = $photo['name'];
            }		
        }
        $this->view->photos = $new_photos;
        $this->view->product = $product;
        $this->view->colors = Product_Model_Color::getMultiOptions();
        $this->view->tags = Product_Model_TagTable::getInstance()->getTagsByProductId($product->product_id);
        $this->view->materials = Product_Model_MaterialTable::getInstance()->getMaterialsByProductId($product->product_id);
        $this->view->user_tags = Product_Model_TagTable::getInstance()->getMyTagsArray($user->user_id);
        $this->view->user_colors = Product_Model_ColorProductTable::getInstance()->findBy('product_id', $product->product_id);
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