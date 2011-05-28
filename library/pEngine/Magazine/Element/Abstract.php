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
abstract class pEngine_Magazine_Element_Abstract
{
    /**
     * @var Zend_Config
     */
    protected $block;

    protected $name;

    protected $type;


    public function getData()
    {
        return $this->hasAdmin() ? $this->loadTemplateAdmin() : $this->loadTemplate();
    }

    abstract protected function loadTemplate();

    abstract protected function loadTemplateAdmin();
    

    /**
     * @param pEngine_Magazine_Block_Abstract $block
     * @return pEngine_Magazine_Element_Abstract
     */
    public function setBlock(pEngine_Magazine_Block_Abstract $block)
    {
        $this->block = $block;
        return $this;
    }

    /**
     * @return pEngine_Magazine_Block_Abstract
     */
    public function getBlock()
    {
        return $this->block;
    }


    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param string $type
     * @return pEngine_Magazine_Element_Abstract
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }



    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return pEngine_Magazine_Element_Abstract
     */
    public function setName($name)
    {
        $this->name=$name;

        return $this;
    }

    /**
     *
     * @return bool
     */
    public function hasAdmin()
    {
        return $this->getBlock()->hasAdmin();
    }

    public function getConfig($name,$default)
    {

        $var = & $this->getBlock()->getConfig('elements')->{$this->getName()}->$name;
        if(isset($var))
            return $var;
        return $default;
    }

}
