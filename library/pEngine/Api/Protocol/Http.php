<?php
/**
 * Description of Http
 *
 * @author yura
 */
class pEngine_Api_Protocol_Http extends pEngine_Api_Protocol_Abstract{

    /**
     * Url
     * @var String
     */
    protected $url;

    /**
     * Format (json|xml|...)
     * @var String
     */
    protected $format='json';

    /**
     * Set url
     * @param String $url
     * @return pEngine_Api_Protocol_Http
     */
    public function setUrl($url){
        $this->url = $url;
        return $this;
    }

    /**
     *
     * @param String $format
     * @return pEngine_Api_Protocol_Http 
     */
    public function setFormat($format){
        $this->format= $format;
        return $this;
    }

    protected function encodingFormat($format,$data){
        switch($format){
            case 'json':
                $var = json_decode($data);
                break;
            case 'xml':
                $var =  simplexml_load_string($data);
                break;
            default:
                $var = $data;
        }

        return $var;
    }

    public function query(){
        $url = $this->url.''.$this->method.'?format='.$this->format;
        $posts = array();
        foreach($this->params AS $key => $value){
            $posts[]=$key.'='.urlencode($value);
        }

        $post = implode('&',$posts);
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => $post,
				'timeout' => 5
            ),
        ));
        try {
            $content = file_get_contents($url, null, $context);
        } catch (Exception $exc) {
            return null;
        }
        
        return $this->encodingFormat($this->format,$content);
    }
}

