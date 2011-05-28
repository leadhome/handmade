<?php

/**
 * Client class for Observable
 * @author vidok
 *
 */
interface pEngine_Observable_Cli
{
	/**
	 * Get message from Observable
	 * @param object $obj
	 * @param int|string|object $param
	 */
	public function notify($obj, $param);
}