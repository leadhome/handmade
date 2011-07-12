<?
class Inc_Auth_UserAdapter implements Zend_Auth_Adapter_Interface
{
	protected $user;
	protected $email = '';
	protected $password = '';

	public function  __construct($email, $password)
	{
		$this->email = $email;
		$this->password = $password;
	}

	public function authenticate() {	
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
	}

	private function createResult($code, $messages = array())
	{
		return new Zend_Auth_Result($code, $this->user, $messages);
	}
}
