<?php
class pEngine_My_Events
{
	/**
     * Added new event
     *
     * $parametrs sample:
     * array (
	 *		'type' => eventtype_id,
	 *		'user_id' => user_id,
     *		'parametrs' => array(
	 *				'user_id' => user_id,
	 *				'user_avatar' => user_avatar
	 *				'user_name' => user_name
	 *			)
	 * )
     *
     * @param array(array) $parametrs
     * @return bool
     */
    public function newEvent($parametrs)
    {
		$data = new My_Model_Events();
		$data->eventstypes_id = $parametrs['type'];
		$data->user_id = $parametrs['user_id'];
		$data->created = new Doctrine_Expression('UNIX_TIMESTAMP()');
		$data->parametrs = $parametrs['parametrs'];
		$data->save();
		return true;
    }
}