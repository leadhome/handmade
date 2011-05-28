<?php

class pEngine_Plugin_ModuleParams extends Zend_Controller_Plugin_Abstract
{
    /**
     * @todo перенести в эту функцию парсинг модулей
     */
    public function  dispatchLoopShutdown()
    {
        parent::dispatchLoopShutdown();
    }

	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
        $request = $this->getRequest();

        /**
		* For testing and console.
	    */
		if(APPLICATION_ENV == 'console')
			return false;

        $layout = Zend_Layout::getMvcInstance();
        $layout->params = $request->getParams();
        
        parent::postDispatch($request);
	}


	/**
	 * Adds path for paginator templates.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 */
	public function  preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$layout = Zend_Layout::getMvcInstance();
		$view = $layout->getView();
		$appScriptPath = realpath(APPLICATION_PATH . '/templates/paginator');
		$view->addScriptPath($appScriptPath);
	}
}