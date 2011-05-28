<?php
/* 
 * Abstract Error
 *
 * @author yura
 */
abstract class pEngine_Acl_Error_Abstract
{
    /*
     * Set option
     * @param array $option
     * @return pEngine_Acl_Error_Abstract
     */
    abstract public function setParams(array $option=null);
    /**
     * Start error
     * @return void
     */
    abstract public function perform();
}
