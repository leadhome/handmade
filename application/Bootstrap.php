<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initOptions()
	{
		$options = $this->getOptions();
		Zend_Registry::set('options', $options);
		$this->bootstrap('doctrine');

		return $options;
	}

	protected function _initResources()
	{
		$loader = new Zend_Loader_Autoloader_Resource(array(
			'basePath' => APPLICATION_PATH . '/modules/index/',
			'namespace' => 'Index'
			));
		$loader->addResourceTypes(array(
			'forms' => array(
				'path' => 'forms',
				'namespace' => 'Forms'
				)
			));
	}

	protected function _initModuleLoaders()
	{ 
		$this->bootstrap('Frontcontroller'); 
		$fc = $this->getResource('Frontcontroller');
		$modules = $fc->getControllerDirectory();
		foreach ($modules AS $module => $dir) {
			$moduleName = strtolower($module);
			$moduleName = str_replace(array('-', '.'), ' ', $moduleName);
			$moduleName = ucwords($moduleName);
			$moduleName = str_replace(' ', '', $moduleName);

			$loader = new Zend_Application_Module_Autoloader(array(
				'namespace' => $moduleName,
				'basePath' => realpath($dir . "/../"),
				));
		}
	}

        public function _initJQuery(){
            //загружаем лэйаут, получаем из него view
            $this->bootstrap('layout');
            $layout=$this->getResource('layout');
            $view=$layout->getView();
            //добавляем директорию, где лежат view-хелперы jquery
            $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
            //включаем jquery, выбираем версию 1.4.2 и версию ui - 1.8 (последние версии)
            //они будут загружаться с серверов гугла.
            //CSS добавляем вручную
            $view->jQuery()
            ->setVersion("1.5.1")
            ->setUiVersion("1.8.14")
            ->addStylesheet("/css/jquery-ui/ui-lightness/jquery-ui-1.8.14.custom.css")
            ->uiEnable();
            return $view; 
        }
        
	protected function _initView()
	{
		// Initialize view
		$view = new Zend_View();
		$view->doctype('HTML5');
		$view->headTitle('');

		// Add it to the ViewRenderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
				'ViewRenderer'
				);
		$viewRenderer->setView($view);

		// Return it, so that it can be stored by the bootstrap
		return $view;
	}

	protected function _initAjax()
	{
		Zend_Controller_Action_HelperBroker::addHelper(
				new ZendX_JQuery_Controller_Action_Helper_AutoComplete()
				);
	}

//	protected function _initZFDebug()
//	{
//		if(!isset($_SERVER['REMOTE_ADDR']) || $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
//			if(!isset($_GET['debugbar']) || $_GET['debugbar'] != date('H'))
//				return;
//		}
//
//		if($_SERVER['REMOTE_ADDR'] != '87.226.228.26' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1')
//			return;
//
//		$autoloader = Zend_Loader_Autoloader::getInstance();
//		$autoloader->registerNamespace('ZFDebug');
//		$autoloader->registerNamespace('Danceric');
//		$options = array(
//				'plugins' => array(
//					'Variables',
//					'File' => array('base_path' => APPLICATION_PATH),
//					'Memory',
//					'Time',
//					'Registry',
//					'Exception',
//					'Html',
//					'ZFDebug_Controller_Plugin_Debug_Plugin_Doctrine'
//					)
//				);
//
//		//Настройка плагина для адаптера базы данных
//		if ($this->hasPluginResource('db')) {
//			$this->bootstrap('db');
//			$db = $this->getPluginResource('db')->getDbAdapter();
//			$options['plugins']['Database']['adapter'] = $db;
//		}
//
//		//Настройка плагина для кеша
//		if ($this->hasPluginResource('cache')) {
//			$this->bootstrap('cache');
//			$cache = $this-getPluginResource('cache')->getDbAdapter();
//			$options['plugins']['Cache']['backend'] = $cache->getBackend();
//		}
//
//		$debug = new ZFDebug_Controller_Plugin_Debug($options);
//
//		$frontController = $this->getResource('frontController');
//		$frontController->registerPlugin($debug);
//	}
	protected function _initFormValidationTranslator() {
        $lang = 'ru';
        $translator = new Zend_Translate(array(
                    'adapter' => 'array',
                    'content' => APPLICATION_PATH . '/lang',
                    'locale' => $lang,
                    'scan' => Zend_Translate::LOCALE_DIRECTORY
                ));
        Zend_Validate_Abstract::setDefaultTranslator($translator);
    }
}

