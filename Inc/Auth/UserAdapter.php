<?
class Inc_Auth_UserAdapter implements Zend_Auth_Adapter_Interface
{
	const NOT_FOUND_MSG = "Account not found";
	const BAD_PW_MSG = "Password is invalid";
	const NOT_ACTIVE_MSG = "You're need to activate your account.";
	const ANOTHER_ERROR_MSG = "Another error.";

	const NOT_FOUND = 1;
	const WRONG_PW = 2;
	const NOT_ACTIVE = 3;
	
	protected $user;
	protected $email = '';
	protected $password = '';

	public function  __construct($email, $password)
	{
		$this->email = $email;
		$this->password = $password;
	}

	public function authenticate() {	
		try {
			$this->user = User_Model_UserTable::getInstance()->getUser($this->email, $this->password);
			$result = new Zend_Auth_Result(Zend_Auth_result::SUCCESS, $this->user);
			
			$locationUser = new Zend_Session_Namespace('locationUser');
			
			$locationUser->city_id = $this->user->city_id;
			$locationUser->city = $this->user->City->title;
			$locationUser->region_id = $this->user->City->Region->region_id;
			$locationUser->region = $this->user->City->Region->title;
			$locationUser->country_id = $this->user->City->Region->country_id;
			$locationUser->country = $this->user->City->Region->Country->title;
			
			return $this->createResult(Zend_Auth_Result::SUCCESS);
		} catch (Exception $ex) {
			if($ex->getMessage() == User_Model_UserTable::WRONG_PW) {
				return $this->createResult(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, array(self::BAD_PW_MSG));
			} elseif($ex->getMessage() == User_Model_UserTable::NOT_FOUND)
				return $this->createResult(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, array(self::NOT_FOUND_MSG));
			elseif($ex->getMessage() == User_Model_UserTable::NOT_ACTIVE)
				return $this->createResult(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, array(self::NOT_ACTIVE_MSG));
			else
				return $this->createResult(Zend_Auth_Result::FAILURE, array(self::ANOTHER_ERROR_MSG));
		}
	}

	private function createResult($code, $messages = array())
	{
		return new Zend_Auth_Result($code, $this->user, $messages);
	}
}
