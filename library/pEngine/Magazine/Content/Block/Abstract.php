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
abstract class pEngine_Magazine_Content_Block_Abstract
    extends pEngine_Magazine_Content_TreeAbstract
{
    /**
     *
     * @var int
     */
    protected $order;

    protected $elementNameNode='blocks';

    protected $element=array();


    protected function getChildren($name=null)
    {
        if(!isset($this->element[$name]))
        {
            $this->createChildren($name);
        }
        /**
         * @var pEngine_Magazine_Element_Abstract $e
         */
        $e = $this->element[$name];

        return $e->setParent($this)->getHtml();
    }

    protected function createChildren($name)
    {
        $type = $this->getConfig('elements')->$name->type;

        $class = 'pEngine_Magazine_Content_Element_'.$type;
        $this->element[$name] =  new $class;
        $this->element[$name]->setName($name)->setType($type);
    }


    protected function loadTemplateAdmin()
    {
        ob_start();
?>
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <th align="right">
            <input type="hidden" name="<?=$this->getHtmlName();?>[order]" value="<?=$this->getOrder();?>">
            Действие: <a href="#" onclick="removeBlock('<?=$this->getParent()->getName();?>');return false;">Удалить</a>
        </th>
    </tr>
    <tr>
        <td>
            <?=$this->TemplateAdmin();?>
        </td>
    </tr>
</table>
<?
        return ob_get_clean();
    }

    protected function setInHtmlDataAdmin()
    {
        if($this->isInHtmlData($this->elementNameNode))
        {
            $this->setInHtmlBlockAllAdmin();
        }

        if($this->isInHtmlData($this->elementNameNode.$this->getType()))
        {
            $this->setInHtmlBlockAdmin();
        }

    }

    protected function setInHtmlData()
    {
        if($this->isInHtmlData($this->elementNameNode))
        {
            $this->setInHtmlBlockAll();
        }

        if($this->isInHtmlData($this->elementNameNode.$this->getType()))
        {
            $this->setInHtmlBlock();
        }

    }

    protected function setInHtmlBlockAllAdmin()
    {
        $this->getView()->headScript()->appendFile('/js/Magazine/Content/Block/AbstractAdmin.js');
        
    }

    protected function setInHtmlBlockAdmin()
    {
        
    }

    protected function setInHtmlBlockAll()
    {

    }

    protected function setInHtmlBlock()
    {

    }

    protected function loadTemplate()
    {
        return $this->TemplateUser();
    }


    public function getHtmlName()
    {
        return $this->getParent()->getHtmlName().'['.$this->getOrder().']['.$this->getName().']';
    }

    public function setParams($params)
    {
        if(!is_array($params))
            return;


        $ElementNames = array_keys($this->getConfig('elements')->toArray());

        foreach($ElementNames AS $element)
        {

            if(isset($params[$element]))
            {
                $this->createChildren($element);
                $this->element[$element]->setParams($params[$element]);
            }
        }
    }

    public function getParams()
    {
        $elements = array();

        foreach($this->element AS $name => $element)
        {
            $elements[$name]=$element->getParams();
        }

        return $elements;
    }

    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }


    abstract protected function TemplateUser();

    abstract protected function TemplateAdmin();
}
