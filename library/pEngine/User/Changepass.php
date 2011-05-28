<?php
/**
 * Class for change user's password
 * @package pEngine
 * @var int $id
 * @var string $oldpassword
 * @var string $password1
 * @var string $password2
 */
class pEngine_User_Changepass
{
	protected $id = "";
	protected $oldpassword = "";
	protected $password1 = "";
	protected $password2 = "";

	/**
	 * Set params
	 * @param int $id
	 * @param string $oldpassword
	 * @param string $password1
	 * @param string $password2
	 */
	public function __construct($id, $oldpassword, $password1, $password2)
	{
		$this->id = $id;
		$this->oldpassword = md5($oldpassword);
		$this->password1 = $password1;
		$this->password2 = $password2;
	}

	/**
	 * Set new password if old password was found and new password and
	 * confirm it coincide
	 * @return bool
	 */
	public function setPassword()
	{
		if($this->oldPassword() && $this->matchPasswords())
		{
			$newpass = Doctrine_Query::create()
				->update('User_Model_User')
				->set('hash', '?', md5($this->password1))
				->where('id = ?', $this->id)
				->execute();

			return true;
		}
		return false;
	}

	/**
	 * Find old password
	 * @return bool
	 */
	protected function oldPassword()
	{
		$check = Doctrine_Query::create()
			->addFrom('User_Model_User')
			->addWhere('hash = ?', $this->oldpassword)
			->addWhere('id = ?', $this->id)
			->count();

		if($check > 0)
			return true;
		
		return false;
	}

	/**
	 * Comparison of the new password and confirm it
	 * @return bool
	 */
	protected function matchPasswords()
	{
		if($this->password1 == $this->password2)
			return true;
		return false;
	}

	/**
	 * Check the possibility to change a user password.
	 * @return bool
	 */
	public static function changePass()
	{
		$ip = new User_Model_User;

		$lastlog = new Zend_Date();
		$log = new pEngine_Log_Log();
		$log->setCode(3);
		$lastlog->set($log->getLastDate($ip->getIp())); //устанавливаем дату последнего лога о смене пароля
		$now = Zend_Date::now(); //сейчас

		$options = Zend_Registry::get('options');
		
		//зачем zend_date, если всё равно так сравниваю
		if(($now->getTimestamp() - $lastlog->getTimestamp()) > $options->change_pass_time)
			return true;
		return false;
	}
}

?>
