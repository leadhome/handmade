<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author yura
 */
abstract class pEngine_Magazine_Content_Maket_Abstract
    extends pEngine_Magazine_Content_TreeAbstract
{
    protected $elementNameNode='maket';

    protected $admin=false;

    /**
     *
     * @var array
     */
    protected $positions=array();

    protected $isInHtmlData=array();

    /**
     *
     * @var Zend_Config
     */
    protected $config;

    protected function getChildren($name=null)
    {
        if(!isset($this->positions[$name]))
        {
            $this->createChildren($name);
        }

        return $this->positions[$name]->getHtml();
    }

    protected function createChildren($name)
    {
            $type = $this->getConfig('positions')->$name->get('type','Default');
            $class = 'pEngine_Magazine_Content_Position_'.$type;
            /**
             * @var pEngine_Magazine_Content_TreeAbstract $p
             */
            $p = new $class;

            $this->positions[$name] = $p->setParent($this)->setType($type)->setName($name);
    }


    public function getHtmlName()
    {
        return $this->elementNameNode;
    }

    public function setParams($params)
    {
        if(!isset($params[$this->elementNameNode]))
            return;

        $this->positions=array();
        $positionNames = array_keys($this->getConfig('positions')->toArray());
        foreach($positionNames AS $name)
        {
            if(isset($params[$this->elementNameNode][$name]))
            {
                $this->createChildren($name);
                $this->positions[$name]->setParams($params[$this->elementNameNode][$name]);
            }
        }
    }

    public function getParams()
    {
        $positions=array();

        foreach($this->positions AS $name => $position)
        {
            $positions[$name] = $position->getParams();
        }

        return array($this->elementNameNode=>$positions);
    }


    protected function isInHtmlData($name)
    {
        $this->isInHtmlData[$name]=true;
        return isset($this->isInHtmlData[$name]);
    }

    public function getHtml()
    {
        $data = parent::getHtml();
        $this->isInHtmlData = array();
        return $data;
    }

    /**
     *
     * @param bool $admin
     * @return pEngine_Magazine_Content_Maket_Abstract
     */
    public function setAdmin($admin)
    {
        $this->admin=$admin;
        return $this;
    }
    
    public function hasAdmin()
    {
        return $this->admin;
    }

    /**
     * Set Default settings
     * @return array
     */
    abstract protected function defConfig();

    /**
     *
     * @return Zend_Config
     */
    public function getConfig($name,$default=null)
    {
        if(!isset($this->config))
            $this->config = new Zend_Config($this->defConfig());
        if(isset($this->config->$name))
            return $this->config->$name;
        return $default;
    }

}
