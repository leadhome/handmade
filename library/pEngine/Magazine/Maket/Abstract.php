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
abstract class pEngine_Magazine_Maket_Abstract
{
    protected $positions=null;
    /**
     *
     * @var Zend_Config
     */
    protected $config;

    protected $admin=false;

    protected $isInHtmlData=array();


    public function getMaket()
    {
        return $this->getTemlate();
    }


    public function loadMaket($data)
    {
        
    }

    public function setAdmin($admin)
    {
        $this->admin=$admin;
    }

    public function hasAdmin()
    {
        return $this->admin;
    }

    public function getHtml()
    {
        $this->isInHtmlData=array();
        parent::getHtml();
    }

    abstract protected function loadTemplate();

    protected function loadTemplateAdmin()
    {
        ob_start();
?>
<div rel="maket">
<?=$this->loadTemplate();?>
</div>
<?
        return ob_get_clean();
    }

    protected function getPosition($position)
    {
        if(!isset($this->positions[$position]))
        {
            $this->positions[$position] = new pEngine_Magazine_Position();
        }

        return  $this->positions[$position]->setName($position)->setMaket($this)->getBlocks();
    }
    
    protected function isInHtmlData($name)
    {
        if(!isset($this->isInHtmlData[$name]))
        {
            $this->isInHtmlData[$name]=true;
            return true;
        }
        else
        {
            return false;
        }
    }

    abstract protected function defConfig();

    /**
     *
     * @return Zend_Config
     */
    public function getConfig()
    {
        if(!isset($this->config))
            $this->config = new Zend_Config($this->defConfig());
        return $this->config;
    }

}
