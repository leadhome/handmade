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
class pEngine_Magazine_Content_Element_Text
    extends pEngine_Magazine_Content_Element_Abstract
{
    protected $text;
    
    protected function loadTemplate()
    {
        return $this->text;
    }

    protected function loadTemplateAdmin()
    {
        ob_start();

        $text = isset($this->text) ? $this->text: $this->textDefault();
        ?>
<textarea cols="50" rows="10" name="<?=$this->getHtmlName();?>"><?=$text;?></textarea>
        <?

        return ob_get_clean();
        
    }

    protected function textDefault()
    {
        return 'Введите свой тест';
    }

    public function setParams($params)
    {
        $this->text = $params;
    }

    public function getParams()
    {
        return $this->text;
    }
}
