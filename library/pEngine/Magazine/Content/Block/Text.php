<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Text
 *
 * @author yura
 */
class pEngine_Magazine_Content_Block_Text
    extends pEngine_Magazine_Content_Block_Abstract
{
    protected function TemplateUser()
    {
        ob_start();
?>
<div>
        <?=$this->getChildren('Text');?>
</div>
<?
        return ob_get_clean();
    }


    protected function TemplateAdmin()
    {
        ob_start();
?>
<div>
    Текстовое поля:
</div>
<div>
        <?=$this->getChildren('Text');?>
</div>
<?
        return ob_get_clean();
    }
}
