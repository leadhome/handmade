<?php 
/**
 * Interface must be implemented by each deletable user content 
 * 
 * @package pEngine
 *
 */
interface pEngine_User_Content
{
	/**
	 * Calls during user delete
	 * 
	 * @param $id UserId
	 */
	public function deleteUser($id);
} 