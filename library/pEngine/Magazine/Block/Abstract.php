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
abstract class pEngine_Magazine_Block_Abstract
{
    /**
     *
     * @var int
     */
    protected $order;
    /**
     *
     * @var string
     */
    protected $type;

    /**
     *
     * @var string
     */
    protected $name;
    /**
     *
     * @var array
     */
    protected $element=array();

    /**
     *
     * @var pEngine_Magazine_Position
     */
    protected $position;

//    public function  __construct()
//    {
//        $class = get_class($this);
//        $name = explode('_',$class);
//        $name = $name[count($name) - 1];
//        $this->type = $name;
//    }

    public function getData()
    {
        ob_start();
?>
<div>
    <?=$this->getElement('Text')?>
</div>
<?
        return ob_get_clean();
    }



    
    protected function getElement($name)
    {
        if(!isset($this->element[$name]))
        {
            $type = $this->getConfig('elements')->$name->type;
            $class = 'pEngine_Magazine_Element_'.$type;
            $this->element[$name] =  new $class;
        }
        /**
         * @var pEngine_Magazine_Element_Abstract $e
         */
        $e = $this->element[$name];

        return $e->setName($name)->setType($type)->setBlock($this)->getData();
    }

    /**
     *
     * @param int $i
     * @return pEngine_Magazine_Block_Abstract
     */
    public function setOrder($i)
    {
        $this->order=$i;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }


    /**
     * @param pEngine_Magazine_Position $position
     * @return pEngine_Magazine_Block_Abstract
     */
    public function setPosition(pEngine_Magazine_Position $position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return pEngine_Magazine_Position
     */
    public function getPosition()
    {
        return $this->position;
    }


    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param string $type
     * @return pEngine_Magazine_Block_Abstract
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
     * @return pEngine_Magazine_Block_Abstract
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
        return $this->getPosition()->hasAdmin();
    }

    public function getConfig($name,$default)
    {
        
        $var = & $this->getPosition()->getConfig('blocks')->{$this->getName()}->$name;
        if(isset($var))
            return $var;
        return $default;
    }
}
