<?php
class pEngine_User_Registration
{
	/**
	 * Checks email for uniqueness
	 * @param string $email
	 * @return int
	 */
	public static function uniqueEmail($email)
	{
		$check = Doctrine_Query::create()
			->addFrom('User_Model_User')
			->addWhere('email = ?', $email)
			->count();
		return $check;
	}

	/**
	 * Checks username for uniqueness
	 * @param string $username
	 * @return int
	 */
	public static function uniqueUsername($username)
	{
		$check = Doctrine_Query::create()
			->addFrom('User_Model_User')
			->addWhere('user_name = ?', $username)
			->count();
		return $check;
	}

	/**
	 * Check account activation.
	 * @param string $email
	 * @return int
	 */
	public static function isActivate($email)
	{
		$check = Doctrine_Query::create()
			->addFrom('User_Model_User')
			->addWhere('email = ?', $email)
			->addWhere('activate = true')
			->count();
		return $check;
	}

	/**
	 * Set activation code for account.
	 * @param int $id
	 * @return string
	 */
	public static function setActivationCode($id)
	{
		$reg = new User_Model_Useractivation;
		$reg->user_id = $id;
		$reg->date = new Doctrine_Expression('NOW()');
		$reg->code = md5(date('D-m-Y H:i:s') . $id . microTime() . md5(rand(0, 1000)));
		$reg->save();
		return $reg->code;
	}

	/**
	 * Activate account.
	 * @param string $code
	 * @return bool
	 */
	public function accountActivation($code)
	{
		$q = Doctrine_Query::create()
			->select('user_id')
			->from('User_Model_Useractivation')
			->where('code = ?', $code)
			->limit('1');
			$user = $q->fetchArray();

		if(isset($user[0]['user_id']))
		{
			$q = Doctrine_Query::create()
				->delete()
				->from('User_Model_Useractivation')
				->where('user_id = ?', $user[0]['user_id'])
				->execute();

			$q = Doctrine_Query::create()
				->update('User_Model_User')
				->set('activate', '?', true)
				->where('id = ?', $user[0]['user_id'])
				->execute();

			return true;
		}
		return false;
	}
}
?>