<?php
/**
 * Description of Factory
 *
 * @author yura
 */
class pEngine_Api {

    /**
     * create new Api protocol
     * @param Zend_Config | Array  $config
     * @return pEngine_Api_Protocol_Abstract
     */
    public static function factory($config=null){

        $option = Zend_Registry::get('options');
        $option = $option['api'];
        $option = new Zend_Config($option, true);

        if(isset($config)){
            if(is_array($config)){
                $config = new Zend_Config($config);
            }
            $option->merge($config);
        }

        $class = 'pEngine_Api_Protocol_'.ucfirst($option->protocol);
        /**
         * @var pEngine_Api_Protocol_Abstract
         */
        $protocol = new $class();

        switch($option->protocol){
            case 'http':
                if(isset($option->url))
                    $protocol->setUrl($option->url);
                if(isset($option->format))
                    $protocol->setFormat($option->format);
                if(isset($option->key))
                    $protocol->setKey($option->key);
        }

        return $protocol;
    }

}
