<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Position
 *
 * @author yura
 */
abstract class pEngine_Magazine_Content_Position_Abstract
    extends pEngine_Magazine_Content_TreeAbstract
{
    /**
     *
     * @var array
     */
    protected $blocks=array();

    protected $elementNameNode='positions';

    protected function getChildren($name=null)
    {

        if(!count($this->blocks) AND $this->getConfig('defaultBlock'))
        {
            $this->createChildren($this->getConfig('defaultBlock'));
        }

        $data = array();
        foreach($this->blocks AS $i => $block)
        {
            $data[] = $block->setOrder($i)->getHtml();
        }
                
        return $data;
    }


    protected function createChildren($name)
    {
        $type = $this->getConfig('blocks')->$name->type;
        $class = 'pEngine_Magazine_Content_Block_'.$type;
        /**
         *
         * @var pEngine_Magazine_Block_Abstract $obj
         */
        $obj = new $class();
        $obj->setName($name)->setType($type)->setParent($this);
        $this->blocks[]=$obj;
        return $obj;
    }

    public function setParams($params)
    {
        if(!is_array($params))
            return;

        $blockNames = array_keys($this->getConfig('blocks')->toArray());
        foreach($params AS $i => $block)
        {
            if(!is_array($block))
                return;
            $blockName = array_shift(array_keys($block));

            if(array_search($blockName, $blockNames)===false)
                continue;
            $this->createChildren($blockName)->setParams($params[$i][$blockName]);
        }
    }

    public function getParams()
    {
        $blocks = array();

        foreach($this->blocks AS $i => $block)
        {
            $blocks[$i][$block->getName()] = $block->getParams();
        }
        return $blocks;
    }

}
