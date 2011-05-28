<?php
/**
 * Authorize users adapter
 * @var object $user
 * @var string $email
 * @var string $password
 *
 */
class pEngine_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
	const NOT_FOUND_MSG = "Account not found";
	const BAD_PW_MSG = "Password is invalid";
	const NOT_ACTIVE_MSG = "You're need to activate your account.";
	const ANOTHER_ERROR_MSG = "Another error.";

	protected $user;
	protected $email = "";
	protected $password = "";
	
	/**
	 * Set params.
	 * @param string $email
	 * @param string $password
	 */
	public function __construct($email, $password)
	{
		$this->email = $email;
		$this->password = $password;
	}

	/**
	 * Trying to auth users.
	 * @return Zend_Auth_Result
	 */
	public function authenticate()
	{
		try
		{
			$this->user = User_Model_User::authenticate($this->email, $this->password);
			$result = new Zend_Auth_Result(Zend_Auth_result::SUCCESS, $this->user);
			return $this->createResult(Zend_Auth_Result::SUCCESS);
		}
		catch (Exception $exc)
		{
			if($exc->getMessage()==User_Model_User::WRONG_PW)
				return $this->createResult(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, array(self::BAD_PW_MSG));
			elseif($exc->getMessage()==User_Model_User::NOT_FOUND)
				return $this->createResult(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, array(self::NOT_FOUND_MSG));
			elseif($exc->getMessage()==User_Model_User::NOT_ACTIVE)
				return $this->createResult(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, array(self::NOT_ACTIVE_MSG));
			else
				return $this->createResult(Zend_Auth_Result::FAILURE, array(self::ANOTHER_ERROR_MSG));
		}

	}

	/**
	 * Creating auth results from code.
	 * @param int $code
	 * @param array $messages
	 * @return Zend_Auth_Result
	 */
	private function createResult($code, $messages = array())
	{
		return new Zend_Auth_Result($code, $this->user, $messages);
	}
}
?>