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
class pEngine_Magazine_Position
{

    /**
     *
     * @var array
     */
    protected $blocks=array();

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var pEngine_Magazine_Maket_Abstract
     */
    protected $maket;

    public function getBlocks()
    {
        if(!count($this->blocks) AND $this->getConfig('defaultBlock'))
        {
            $this->createBlock($this->getConfig('defaultBlock'));
        }

        $data = array();
        foreach($this->blocks AS $i => $block)
        {
            $data[] = $block->setOrder($i)->getData();
        }

        return implode('',$data);
    }



    protected function createBlock($name)
    {
        $type = $this->getConfig('blocks')->$name->type;
        $class = 'pEngine_Magazine_Block_'.$type;
        /**
         * @var pEngine_Magazine_Block_Abstract $obj
         */
        $obj = new $class();
        $obj->setName($name)->setType($type)->setPosition($this);
        $this->blocks[]=$obj;
    }

    /**
     *
     * @param pEngine_Magazine_Maket_Abstract $maket
     * @return mixed
     */

    public function getConfig($name,$default=null)
    {
        $var = & $this->getMaket()->getConfig()->positions->{$this->getName()}->$name;
        if(isset($var))
            return $var;
        return $default;
    }

    /**
     *
     * @param pEngine_Magazine_Maket_Abstract $maket
     * @return pEngine_Magazine_Position
     */

    public function setMaket($maket)
    {
        $this->maket = $maket;

        return $this;

    }

    /**
     *
     * @return pEngine_Magazine_Maket_Abstract
     */
    public function getMaket()
    {
        return $this->maket;
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
     * @return pEngine_Magazine_Position
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
        return $this->getMaket()->hasAdmin();
    }

}
