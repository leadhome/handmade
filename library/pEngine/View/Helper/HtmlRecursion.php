<?php

class pEngine_View_Helper_HtmlRecursion extends Zend_View_Helper_Abstract
{
	public function htmlRecursion ($script, $args=null, $argsFieldName='args')
	{
		$v = clone $this->view;
		$v->assign($argsFieldName, $args);
		echo $v->render($script);
	}
}