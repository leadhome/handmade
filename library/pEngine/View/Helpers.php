<?php
/*
 * Помошники для View
 * 
 */

class pEngine_View_Helpers
{
	/*
	 * View
	 * @var pEngine_View_Smarty
	 */
	protected $view;
	
	/**
	 * Конструктор
	 * @param $view pEngine_View_Smarty
	 */
	
	public function __construct(pEngine_View_Smarty $view)
	{
		
        $view->addHelperPath('pEngine/View/Helper','pEngine_View_Helper_');
        //print_r($view->getHelperPaths());
        $this->view=$view;


	}
	
    /**
     * Accesses a helper object from within a script.
     *
     * If the helper class has a 'view' property, sets it with the current view
     * object.
     *
     * @param string $name The helper name.
     * @param array $args The parameters for the helper.
     * @return string The result of the helper output.
     */
    public function __call($name, $args)
    {
        // is the helper already loaded?
        $helper = $this->view->getHelper($name);

        // call the helper method
        return call_user_func_array(
            array($helper, $name),
            $args
        );
    }
    
    /**
     * Заглушка для helper Action
     */
//    public function action(){}
    
    
	
}
