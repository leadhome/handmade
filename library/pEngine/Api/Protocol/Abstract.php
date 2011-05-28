<?php
/**
 * Description of protocol
 *
 * @author yura
 */
abstract class pEngine_Api_Protocol_Abstract {

    /**
     * Params
     * @var Array
     */
    protected $params=array();

    /**
     * Method (user.info)
     * @var string
     */
    protected $method=null;

    /**
     * Set method
     * @param String $name
     * @return pEngine_Api_Protocol_Abstract
     */
    public function setMethod($name){
        $this->method = $name;
        return $this;
    }

    /**
     * Set param
     * @param String $name
     * @param mixed $value
     * @return pEngine_Api_Protocol_Abstract
     */
    public function setParam($name,$value){
        $this->params[$name] = $value;
        return $this;
    }
    /**
     * Set params
     * @param Array $array
     * @return pEngine_Api_Protocol_Abstract
     */
    public function setParams($array){
        $this->params = array_merge($this->params, $array);
        return $this;
    }
    /**
     * Clean params
     * @return pEngine_Api_Protocol_Abstract
     */
    public function cleanParams(){
        $this->params = array();
        return $this;
    }

    public function setKey($key){
        $this->setParam('__key', $key);
        return $this;
    }

    abstract public function query();
}