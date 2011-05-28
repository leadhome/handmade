<?php
/**
 * Class for registration new users.
 * @package pEngine
 * @var string $email
 * @var string $user_name
 * @var string $password
 * @var datetime $joined_date
 * @var bool $avtivation
 */
class pEngine_User_Regadapter
{
	protected $email = "";
	protected $user_name = "";
	protected $password = "";
	protected $joined_date = "";
	protected $activation = false;
	protected $sex = "";

	/**
	 * Set params.
	 * @param string $email
	 * @param string $username
	 * @param string $password
	 */
	public function  __construct($email, $username, $password, $sex)
	{
		$this->email = $email;
		$this->user_name = $username;
		$this->password = md5($password);
		$this->sex = $sex;
	}

	/**
	 * Set user data in database.
	 *
	 * If a user comes under any reasonable openID then it creates a profile,
	 * it will have all-but change the password to access the site through authorization
	 * @param bool $activate
	 * @return int
	 */
	public function registration($activate = true)
	{
		$reg = new User_Model_User();
		$reg->user_name = $this->user_name;
		$reg->hash = $this->password;
		$reg->email = $this->email;
		$reg->joined_date = new Doctrine_Expression('NOW()');
		$reg->activate = $activate;
		$reg->save();

		$prof = new Field_Model_Value();
		$prof->user_id = $reg->id;
		$prof->value = $this->sex;
		$prof->field_id = 5;
		$prof->save();
		
		return $reg->id;
	}
}

?>
