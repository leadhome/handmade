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
        $this->_helper->AjaxContext()->addActionContext('ajaxmaterial', 'json')->initContext('json');
        $this->_helper->AjaxContext()->addActionContext('ajaxtags', 'json')->initContext('json');
    }
    
    public function ajaxmaterialAction()
    {
        if ($term=trim($this->_request->getParam('term'))) {
        $res = Doctrine_Query::create()
                ->from('Product_Model_Material')
                ->where("title LIKE '$term%'")
                ->limit(10)
                ->execute()
                ->toKeyValueArray('material_id', 'title');
        $result=array();
        
        foreach ($res as $re) {
        $result[]=$re;
        }
        #Zend_Debug::dump($result);
        echo $this->_helper->json($result);
        } else $this->_redirect('/');
    }
    
    public function ajaxtagsAction()
    {
        if ($term=$this->_request->getParam('term')) 
        {
            $res = Doctrine_Query::create()
                            ->from('Product_Model_Tag')
                            ->where("title LIKE '$term%'")
                            ->limit(10)
                            ->execute()
                            ->toKeyValueArray('tag_id', 'title');
            $result=array();
            foreach ($res as $re) {
            $result[]=$re;
            }
            echo $this->_helper->json($result);
        } else $this->_redirect('/');
    }
    
    public function indexAction()
    {

    }
    
    public function get2categorysAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
            $parentId = (int) $this->_getParam('parent_id');
            if ($parentId <= 0)
                throw new Zend_Controller_Action_Exception('parent_id must > 0');
            $subCategories = Doctrine_Query::create()
                                ->from('Product_Model_Category')
                                ->where('parent_id = ?', $parentId)
                                ->execute()
                                ->toKeyValueArray('category_id', 'title');
            $json = Zend_Json::encode($subCategories);
            $this->getResponse()->appendBody($json);
        } else $this->_redirect('/');
    }
    
    public function addAction()
    {
        #if (!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect("/");
                
        $form = new Product_Form_Add();
        $form->setAction('/product/index/add')->setMethod('post');
        $this->view->colors = Product_Model_Color::getMultiOptions();
        
        if ( $this->getRequest()->isPost() ) {
            $values = $form->getValues();
            
            if ($form->isValid($values)) {   
                $this->view->error = 0;
                if (!$this->getRequest()->isXmlHttpRequest()) {

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
        $this->_redirect('');
    }
}

