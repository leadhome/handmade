<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TreeAbstract
 *
 * @author yura
 */
abstract class pEngine_Magazine_Content_TreeAbstract
{
    /**
     * Name node
     * @var string
     */
    protected $elementNameNode;

    /**
     *
     * @var pEngine_Magazine_Content_TreeAbstract
     */
    protected $parent;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

 
    abstract protected function getChildren($name=null);

    abstract protected function createChildren($name);

    abstract protected function loadTemplate();

    abstract protected function loadTemplateAdmin();

    public function getHtml()
    {
        if($this->hasAdmin())
        {
            $this->setInHtmlDataAdmin();
            $data = $this->loadTemplateAdmin();
        }
        else
        {
            $this->setInHtmlData();
            $data = $this->loadTemplate();
        }

        return $data;
    }

    /**
     * This 2 function set js or css script in html ...
     *
     */
    protected function setInHtmlData()
    {

    }

    protected function setInHtmlDataAdmin()
    {

    }

    /**
     *
     * @return Zend_View_Abstract
     */

    public function getView()
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
    }

    protected function isInHtmlData($name)
    {
        return $this->getParent()->isInHtmlData($name);
    }

    /**
     * @param pEngine_Magazine_Content_TreeAbstract $parent
     * @return pEngine_Magazine_Content_TreeAbstract
     */
    public function setParent(pEngine_Magazine_Content_TreeAbstract $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return pEngine_Magazine_Content_TreeAbstract
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get this Type Content
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param string $type
     * @return pEngine_Magazine_Content_TreeAbstract
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }



    /**
     *  Get this Name Content
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return pEngine_Magazine_Content_TreeAbstract
     */
    public function setName($name)
    {
        $this->name=$name;

        return $this;
    }

    public function getHtmlName()
    {
        return $this->getParent()->getHtmlName().'['.$this->getName().']';
    }

    public function setParams()
    {
        
    }

    public function getParams()
    {
        
    }


    /**
     * Is Admin Edit
     * @return bool
     */
    public function hasAdmin()
    {
        return $this->getParent()->hasAdmin();
    }

    public function getConfig($name,$default=null)
    {
        $var = $this->getParent()->getConfig($this->elementNameNode);
        if(isset($var))
        {
            if(isset($var->{$this->getName()}))
            {
                $var = $var->{$this->getName()};

                if(isset($var->$name))
                {
                    return $var->$name;
                }
            }

        }
        return $default;
    }
}
