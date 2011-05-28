<?php

class pEngine_View_Helper_Urlwithoutroute extends Zend_View_Helper_Abstract
{
	public function urlwithoutroute($param)
	{
//		$param = 'page';
//		$this->serverUrl($this->url()) = $_SERVER['REQUEST_URI']
//		$server_url = new Zend_View_Helper_ServerUrl();
//		$url = $server_url->serverUrl(true);
//		die($this->view->serverUrl(true));
//		$cur_url = new Zend_View_Helper_Url();
		$url = preg_replace('/(\?|&)' . $param . '=(\d+)/', '', $this->view->serverUrl(true));
		$url .= preg_match('/\?/i', $url) ? '&' . $param . '=' : '?' . $param . '=';
		return $url;
	}
}