<?php
class pEngine_View_Filter_Translate implements Zend_Filter_Interface
{
 
    /**
     * Начальный разделить блока перевода в Виде
     *
     */
    const I18N_DELIMITER_START = '<i18n>';
 
    /**
     * Заключающий разделить блока в Виде
     *
     */
    const I18N_DELIMITER_END = '</i18n>';
 
    /**
     * Фильтрация значения для текста внутри тегов i18n и перевод
     * 
     * @param string $value
     * @return string
     */
    public function filter($value) 
    {
        $startDelimiterLength = strlen(self::I18N_DELIMITER_START);
        $endDelimiterLength = strlen(self::I18N_DELIMITER_END);
 
        $translator = Zend_Registry::get('Zend_Translate');

        $offset = 0;
        while (($posStart = strpos($value, self::I18N_DELIMITER_START, $offset)) !== false) {
            $offset = $posStart + $startDelimiterLength;
            if (($posEnd = strpos($value, self::I18N_DELIMITER_END, $offset)) === false) {
                throw new Zx_Exception("No ending tag after position [$offset] found!");        
            }
            $translate = substr($value, $offset, $posEnd - $offset);
 
            $translate = $translator->_($translate);
 
            $offset = $posEnd + $endDelimiterLength;
            $value = substr_replace($value, $translate, $posStart, $offset - $posStart);
            $offset = $offset - $startDelimiterLength - $endDelimiterLength;
        }
 
        return $value;    
    }
}
