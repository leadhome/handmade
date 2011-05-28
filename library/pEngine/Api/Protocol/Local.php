<?php
/**
 * Description of Local
 *
 * @author yura
 */
class pEngine_Api_Protocol_Local extends pEngine_Api_Protocol_Abstract{

    public function query(){
        $server = new API_Server_Local();
		list($class, $method) = explode('.',  $this->method);
        $server->setMethod($method);
        $server->setClass($class);
        $server->setParams($this->params);
        return $server->query();
    }
}

