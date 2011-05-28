<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Position
 *
 * @author yura
 */
class pEngine_Magazine_Content_Position_Default
    extends pEngine_Magazine_Content_Position_Abstract
{


    protected function loadTemplate()
    {
        ob_start();

        $this->AddLinkNewBlock();
        $blocks = $this->getChildren();
        foreach($blocks AS $block)
        {
?>
<div>
<?
            echo $block;
            echo '<br/>';
            $this->AddLinkNewBlock();
?>
</div>
<?
        }



        return ob_get_clean();
        
    }

    protected function loadTemplateAdmin()
    {
        return $this->loadTemplate();
    }

    protected function AddLinkNewBlock()
    {
        $countBlocksMax = $this->getConfig('countBlocks',-1);
        $count = count($this->blocks);
        if($this->hasAdmin() AND ($countBlocksMax==-1 OR $count<$countBlocksMax))
        {
?>
<br/><a href="./edit">Добавить блок</a><br/>
<?
        }
    }
}
