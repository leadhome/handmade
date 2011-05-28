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
class pEngine_Magazine_Maket_Article_Default extends pEngine_Magazine_Maket_Abstract
{

    protected function loadTemplate()
    {
        ob_start();
?>
<table width="100%">
    <tr>
        <td><?=$this->getPosition('index');?></td>
        <td>---</td>
    </tr>
</table>
<?
        return ob_get_clean();
    }

    protected function defConfig()
    {

        return array(
        'positions'=>array(
            'index' => array(
                'defaultBlock'=>'One',
                'countBlocks'=>-1,
                'blocks'=>array(
                    'One'=>array(
                        'delete'=>true,
                        'type'=>'Text',
                        'elements'=>array(
                            'Text'=>array(
                                'type'=>'Text',
                                'align'=>'center'
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
                            )
                        )
                    )
                )
            )
        )
      );
    }

}
