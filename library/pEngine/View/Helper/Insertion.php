<?

/**
 * Прототип $this->action с параметром урла
 *
 * @category	pEngine
 * @package		pEngine_View
 * @subpackage	Helper
 *
 * @param		$url урл вида /firm/34 
 */
class pEngine_View_Helper_Insertion
{
    public function insertion($url)
	{
		$front = Zend_Controller_Front::getInstance();
		$uri = 'http://' . $_SERVER['SERVER_NAME'] . $url;
		$request = new Zend_Controller_Request_Http($uri);
		$request = $front->getRouter()->route($request);

		$params = $request->getParams();
		$other_params = null;
		foreach($params as $key => $value){
			if($key != 'module' && $key != 'controller' && $key != 'action'){
				$other_params[$key] = $value;
			}
		}

        $helper = new Zend_View_Helper_Action();
        return $helper->action($params['action'], $params['controller'], $params['module'], $other_params);
    }
}
