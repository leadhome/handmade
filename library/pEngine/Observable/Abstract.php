<?php

/**
 * Abstract Observable classs
 * @author Dmitriy Burlutskiy
 */
abstract class pEngine_Observable_Abstract
{
	protected $clients = array();

	/**
	 * Add client in Observer
	 * @param object $cli
	 */
	public function attach(pEngine_Observable_Cli $cli)
	{
		if (is_object($cli))
			$this->clients[] = $cli;
	}
	
	/**
	 * Delete client in Observer
	 * @param object $cli
	 */
	public function detach(pEngine_Observable_Cli $cli)
	{
		$newClients = array();
		
		if (count($this->clients) > 0)
			foreach ($this->clients as $client)
				if ($client !== $cli)
					$newClients[] = clone $client;
					
		$this->clients = $newClients;
	}
	
	/**
	 * send messagers to clients
	 */
	abstract public function notify($obj, $param);		
}