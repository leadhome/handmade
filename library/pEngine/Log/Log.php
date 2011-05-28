<?php
/**
 * Class for logging
 * @package pEngine
 * @var int $log_code
 * @var int $user_id
 * @var string $user_ip
 * @var int $change_pass_time
 */
class pEngine_Log_Log
{
	protected $log_code = "";
	protected $user_id = null;
	protected $user_ip = "";
	protected $change_pass_time = "";

	/**
	 * Set params.
	 */
	public function  __construct()
	{
		if(isset(Zend_Auth::getInstance()->getIdentity()->id))
			$this->user_id = Zend_Auth::getInstance()->getIdentity()->id;
		
		$this->user_ip = $this->getIp();

		$options = Zend_Registry::get('options');
		$this->change_pass_time = $options->change_pass_time;
	}
    
    /**
	 * Set log code.
	 * @param int $code
	 */
	public function setCode($code)
	{
		$this->log_code = $code;
	}

	/**
	 * Forced entry
	 * @param int $id
	 */
	public function setUserId($id)
	{
		$this->user_id = $id;
	}

	/**
	 * Set ip only for searching
	 * @param string $ip
	 */
	public function setUserIp($ip)
	{
		$this->user_ip = $ip;
	}


	/**
	 * Get user ip
	 * @return string
	 */
	protected function getIp()
	{
		$ip = new User_Model_User;
		return $ip->getip();
	}


	/**
	 * Save the log in database
	 */
	public function save()
	{
		$log = new Log_Model_Log();
		$log->log_code_id = $this->log_code;
		$log->date = new Doctrine_Expression('NOW()');
		$log->ip = $this->user_ip;
		$log->user_id = $this->user_id;
		$log->save();
		
	}

	/**
	 * Delete log from database
     * @todo If there are no conditions to purify the whole log
	 */
	public function delete()
	{
		$del_query = Doctrine_Query::create();
		$del_query->addFrom('Log_Model_Log');
		if(!empty($this->user_ip))
				$del_query->addWhere('ip = ?', $this->user_ip);

		if(!empty($this->user_id))
			$del_query->addWhere('user_id = ?', $this->user_id);

		if(!empty($this->log_code))
			$del_query->addWhere('log_code_id = ?', $this->log_code);

		$del_query->delete();
		$del_query->execute();
	}

	/**
	 * Count log entries in database
	 * @return int
	 */
	public function count()
	{
		$del_query = Doctrine_Query::create();
		$del_query->addFrom('Log_Model_Log');
		if(!empty($this->user_ip))
				$del_query->addWhere('ip = ?', $this->user_ip);

		if(!empty($this->user_id))
			$del_query->addWhere('user_id = ?', $this->user_id);

		if(!empty($this->log_code))
			$del_query->addWhere('log_code_id = ?', $this->log_code);

		return $del_query->count();
	}

	/**
	 * Output logs from the database, sorted by date.
	 * @return array
	 */
	public function getLogs()
	{
		$list = Doctrine_Query::create()
			->from('Log_Model_Log')
			->orderBy('date DESC')
			->fetchArray();
		return $list;
	}

	/**
	 * Return the last date for the log code and ip.
	 * @param string $ip
	 * @param int $code
	 * @return Zend_Date
	 */
	public function getLastDate($ip)
	{
		$list = Doctrine_Query::create()
			->select('date')
			->from('Log_Model_Log')
			->addWhere('log_code_id = ?', $this->log_code)
			->addWhere('ip = ?', $ip)
			->orderBy('date DESC')
			->limit(1)
			->execute();
		if(!empty($list[0]['date']))
			return $list[0]['date'];
		else
		{
			$date =new Zend_Date('17.01.1988');
			$date->toString();
			return $date;
		}
	}

	/**
	 * Check the possibility to change a user password.
	 * @return bool
	 */
//	public function changePass()
//	{
//		$lastlog = new Zend_Date();
//		$lastlog->set($this->getLastDate($this->getIp(), 3)); //устанавливаем дату последнего лога о смене пароля
//		$now = Zend_Date::now(); //сейчас
//
//		//зачем zend_date, если всё равно так сравниваю
//		if(($now->getTimestamp() - $lastlog->getTimestamp()) > $this->change_pass_time)
//			return true;
//		return false;
//	}
}

?>
