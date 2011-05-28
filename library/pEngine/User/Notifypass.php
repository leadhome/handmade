<?php
/**
 * Class for remind the user's password.
 *
 * The user sends an email address and receive confirmation code,
 * then enters the code and receive an e-mail new generated password.
 * @package pEngine
 * @var array $options
 * @var string $user_email
 */
class pEngine_User_Notifypass
{
	protected $options;
	protected $user_email;

	/**
	 * Set options from Zend_Registry.
	 */
	public function __construct()
	{
		$this->options = Zend_Registry::get('options');
	}

	/**
	 * Generate random string from array.
	 * @param int $num
	 * @return string
	 */
	protected function generator($num)
	{
		$arr = array('a','b','c','d','e','f','h','j','k','m','n','p','r','s','t','u','v','x','y','z','A','C','D','E','F','G','H','J','K','L','M','N','P','R','S','T','U','V','X','Y','Z','2','3','4','5','6','7','8');
		$pass = "";
		for($i = 0; $i < $num; $i++)
		{
			$index = mt_rand(0, count($arr) - 1);
			$pass .= $arr[$index];
		}
		return $pass;
	}

	/**
	 * Get user id from database.
	 * @param string $email
	 * @return int 
	 */
	public function getUserId($email)
	{
		$user_id = Doctrine_Core::getTable('User_Model_User')->findOneByEmail($email);
		if(!empty($user_id->email) && !empty($user_id->id))
		{
			$this->user_email = $user_id->email;
			return $user_id->id;
		}
		
		return 0;
	}

	/**
	 * Sets the activation code to change password.
	 * @param int $id
	 * @return string
	 */
	public function setNotificationCode($id)
	{
		$user_id = Doctrine_Query::create()
			->delete()
			->from('User_Model_Useractivation')
			->where('user_id = ', $id)
			->execute();
		
		$reg = new User_Model_Useractivation;
		$reg->user_id = $id;
		$reg->date = new Doctrine_Expression('NOW()');
		$reg->code = $this->generator($this->options->notify_code_length);
		$reg->save();

		$m = new Zend_Mail();
		$m->addTo($this->user_email);
		$m->setFrom($this->options->noreply_email, 'noreply');
		$m->setSubject('Notification code');
		$m->setBodyText('To change your password, go to http://'. $this->options->server_name .'/user/registration/changepass?notificationcode=' . $reg->code . ' Your notification code is ' . $reg->code);
		$m->send();

		return $reg->code;
	}

	/**
	 * Returns the user id by the activation code to change password.
	 * @param string $code
	 * @return int
	 */
	public function getUserByNotifyCode($code)
	{
		$user_id = Doctrine_Core::getTable('User_Model_Useractivation')->findOneByCode($code);
		if($user_id)
			return $user_id->user_id;

		return 0;
	}


	/**
	 * Set new password for user and send it on user's email.
	 * @param int $id
	 * @return bool
	 */
	public function setPassword($id)
	{
		$newpass = $this->generator($this->options->user_password_length);

		$query = Doctrine_Query::create()
			->update('User_Model_User')
			->set('hash', '?', md5($newpass))
			->where('id = ?', $id)
			->execute();

		$deleteact = Doctrine_Query::create()
			->delete()
			->from('User_Model_Useractivation')
			->where('user_id = ?', $id)
			->execute();

		//Delete all failed login attempts
		$logs = new pEngine_Log_Log();
		$logs->setCode(1);
		$logs->delete();

		//Set log after change password
		$logs = new pEngine_Log_Log();
		$logs->setCode(3);
		$logs->save();

		$user_email = Doctrine_Core::getTable('User_Model_User')->findOneById($id);

		$m = new Zend_Mail();
		$m->addTo($user_email->email);
		$m->setFrom($this->options->noreply_email, 'noreply');
		$m->setSubject('New password');
		$m->setBodyText('Your new password is ' . $newpass);
		$m->send();

		return true;
	}
}

?>
