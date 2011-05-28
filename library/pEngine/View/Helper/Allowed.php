<?
/**
 * Helper to access to some view 
 *
 * @category   pEngine
 * @package   pEngine_View
 * @subpackage Helper
 */

class pEngine_View_Helper_Allowed extends Zend_View_Helper_Abstract
{
	/**
	* Access to some view (template)
    * 
	* @param string $action Action name for controller  
	* @param null|string $contoller Controller name 
	* @param null|string $module Module name
	* @return boolean
	*/

	public function allowed($action,$controller=null,$module=null)
	{
		return pEngine_Acl::factory($controller,$module)->isAllowed($action);
	}
}
