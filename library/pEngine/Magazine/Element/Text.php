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
class pEngine_Magazine_Element_Text extends pEngine_Magazine_Element_Abstract
{
    protected $text='asdfdf';
    
    protected function loadTemplate()
    {
        return $this->text;
    }

    protected function loadTemplateAdmin()
    {
        ob_start();

        $text = isset($this->text) ? $this->text: $this->textDefault();
        ?>
        <textarea cols="50" rows="10"><?=$text;?></textarea>
        <?

        return ob_get_clean();
        
    }

    protected function textDefault()
    {
        return 'Введите свой тест';
    }
}
