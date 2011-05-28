<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Plugin
 *
 * @author yura
 */
class pEngine_Component_Plugin extends Zend_Controller_Plugin_Abstract
{
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{

        $request->setParam('component', $request->getParam('component', 'default'));
	}
}