<?php
class pEngine_My_ObCli implements pEngine_Observable_Cli
{
    const NEW_EVENT = "newEvent";

	/**
     * $param sample:
	 *
     * array (
	 *		'method' => pEngine_My_ObCli::NEW_EVENT,
	 *		'type' => eventtype_id,
	 *		'user_id' => user_id,
     *		'parametrs' => array(
	 *				'user_id' => user_id,
	 *				'user_avatar' => user_avatar
	 *				'user_name' => user_name
	 *			)
	 * )
     *
     * @param pEngine_My_Events $obj
     * @param array $param
     */
    public function notify($obj, $param)
    {
        if (!($obj instanceof pEngine_My_Events))
            return false;

        if (method_exists($this, $param['method']))
            $this->$param['method']($obj, $param);
    }

	/**
     * Added new event
     * The type to see in the table: my__model__events_types
	 *
     * $param sample:
     * array (
	 *		'method' => method,
	 *		'type' => eventtype_id,
	 *		'user_id' => user_id,
     *		'parametrs' => array(
	 *				'user_id' => user_id,
	 *				'user_avatar' => user_avatar
	 *				'user_name' => user_name
	 *			)
	 * )
	 *
     * @param pEngine_My_Events $obj
     * @param array $param
     */
    private function newEvent($obj, $param)
    {
		$parametrs = new stdClass();
		foreach ($param['parametrs'] as $key => $value)
			$parametrs->$key = $value;
		$param['parametrs'] = serialize($parametrs);
        $obj->newEvent($param);
    }
}