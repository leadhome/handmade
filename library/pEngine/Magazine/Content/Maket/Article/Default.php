<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Default
 *
 * @author yura
 */
class pEngine_Magazine_Content_Maket_Article_Default 
    extends pEngine_Magazine_Content_Maket_Abstract
{

    protected function loadTemplate()
    {
        ob_start();
?>
<div align="center">
<table width="1000">
    <tr>
        <td><?=$this->getChildren('index');?></td>
    </tr>
</table>
</div>
<?
        return ob_get_clean();
    }

    protected function loadTemplateAdmin()
    {
        return $this->loadTemplate();
    }

    protected function defConfig()
    {

        return array(
        'positions'=>array(
            'index' => array(
                'defaultBlock'=>'One',
                'countBlocks'=>3,
                'blocks'=>array(
                    'One'=>array(
                        'delete'=>true,
                        'type'=>'Text',
                        'elements'=>array(
                            'Text'=>array(
                                'type'=>'Text',
                                'align'=>'center'
                            ),
                            'Text2'=>array(
                                'type'=>'Text',
                                'align'=>'left'
                            )
                        )
                    ),
                    'Two'=>array(
                        'delete'=>true,
                        'type'=>'Text',
                        'elements'=>array(
                            'Text'=>array(
                                'type'=>'Text',
                                'align'=>'center'
                            ),
                            'Text2'=>array(
                                'type'=>'Text',
                                'align'=>'left'
                            )
                        )
                    )
                )
            )
        )
      );
    }

}
