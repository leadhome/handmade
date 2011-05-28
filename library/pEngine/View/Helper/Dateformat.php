<?php 

class pEngine_View_Helper_Dateformat extends Zend_View_Helper_Abstract
{
    public function dateformat( $date )
    {
		$monts = array('январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');
        $timestamp = strtotime($company->created);
        $date = getdate($timestamp);
		return $date['mday']. ' '. $months[$date['mon']+1] . ', ' . $date['hours'] . ':' . $date['minutes'];
    } 
}

?>
