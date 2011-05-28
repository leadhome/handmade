<?php

/**
 * Плагин для проверки доступа
 * 
 * @author yura
 *
 */
class pEngine_Acl_Plugin extends Zend_Controller_Plugin_Abstract
{
    /**
     * Exception ACL when not allowed for action
     */
    const EXCEPTION_DENY='EXCEPTION_DENY';

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$acl = pEngine_Acl::factory()->access($request->getActionName());
	}
}