<?php
class pEngine_Plugin_View extends Zend_Controller_Plugin_Abstract
{

    /**
     * @todo перенести в эту функцию парсинг модулей
     */
    public function  dispatchLoopShutdown()
    {
        parent::dispatchLoopShutdown();
    }

	/**
	 * View Plugin
	 *
	 * Plugin sets layouts for routes. Many routes can use one layout.
	 * Layout file placed in * /templates/%router.module%/%layout.name%.phtml
	 * where
	 * %router.module% is value of module field from router table;
	 * %layout.name% is value of name field from layout table;
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @todo обработка ошибок
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{

        $request = $this->getRequest();

        /**
		 * For testing and console.
		 */
		if(APPLICATION_ENV == 'console')
			return false;

        //@todo Сделать одним запросом
		$routes = Doctrine_Query::create()
			->from('Router_Model_Route r')
            ->leftJoin('r.Template_Model_Layout l')
            ->leftJoin('l.ModuleInLayout')
			->where('r.id = ?', pEngine_Router::getId(
                                    Zend_Controller_Front
                                    ::getInstance()
                                    ->getRouter()
                                    ->getCurrentRouteName()
                                    )
                                )
			->execute();
        
        $layout = Zend_Layout::getMvcInstance();

        //if ajax then disable render layout
        if ($this->getRequest()->isXmlHttpRequest())
        {
            Zend_Controller_Action_HelperBroker::removeHelper('ViewRenderer');
            $layout->disableLayout();
        }
        else
        {
			//set layout if enabled
			foreach($routes as $route)
				if($route->Template_Model_Layout->enabled)
					$layout->setLayout($route->module . '/' .
						$route->Template_Model_Layout->name);
                
            $view = $layout->getView();

            foreach($routes as $r)
                foreach($r->Template_Model_Layout->ModuleInLayout as $ml)
                {
					//only enabled modules
					if($ml->enabled && $ml->Template_Model_Module->enabled)
					{
						$pos = $ml->position;
						//@todo try unserialize
                        $request->setParam('modulesParam', unserialize($ml->params));
						$layout->$pos.=
							$view->action
							(
								$ml->Template_Model_Module->action,
								$ml->Template_Model_Module->controller,
								$ml->Template_Model_Module->module,
                                $request->getParams()
							);
					}
                }

             /**
             * Set individual for subdomain header.
             * @todo use partial for header and footer not helper!!!
             */
            $layout->header =
                    $view->action
                        (
                            Zend_Registry::get('options')->domain_name,
                            'header',
                            'template'
                        );

        }

			/**
			 * @todo Set individual for subdomain footer.
			 */

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
?>
