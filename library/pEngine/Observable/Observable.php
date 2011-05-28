<?php

/**
 * Global Observable classs
 * @author Dmitriy Burlutskiy
 */
class pEngine_Observable_Observable extends pEngine_Observable_Abstract
{
	private static $_instance = null;
	
	/**
	 * constructor
	 */
	private function __construct()
	{
		
	}
	
	/**
	 * Getting object of Observer
	 * @return pEngine_Observable_Observable
	 */
	static public function getInstance()
	{
		if (self::$_instance == null)
			self::$_instance = new self;
			
		return self::$_instance;
	}
	
	/**
	 * Sends messagers to client 
	 * @param $obj object
	 * @param $param string|const
	 */
	public function notify($obj, $param)
	{
		foreach ($this->clients as $cli)
			$cli->notify($obj, $param);
	}
}