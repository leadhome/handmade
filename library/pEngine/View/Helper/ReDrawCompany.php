<?php
class pEngine_View_Helper_ReDrawCompany
{

	protected $_monday;
	protected $_tuesday;
	protected $_wednesday;
	protected $_thursday;
	protected $_friday;
	protected $_saturday;
	protected $_sunday;

	protected $_startend_separator;
	protected $_time_separator;

	protected $separator;
	protected $spacer;

	public function __construct()
	{
		$this->_monday = 'Пн';
		$this->_tuesday = 'Вт';
		$this->_wednesday = 'Ср';
		$this->_thursday = 'Чт';
		$this->_friday = 'Пт';
		$this->_saturday = 'Сб';
		$this->_sunday = 'Вс';

		$this->_startend_separator = '...';
		$this->_time_separator = '.';

		$this->separator = '-';
		$this->spacer = ' ';
	}

	public function drawCompany($company, $index, $admin = false)
	{
        $string = '';
		$string .= '<script type="text/javascript">';
            $string .= 'jQuery( document ).ready( function() {';
                $string .= '$( \'a#SendError_' . $company->id . '\' ).reports( { type : \'firms\', id : ' . $company->id . ', email_enabled : true } )';
            $string .= '} );';
        $string .= '</script>';
        $string .= '<script type="text/javascript">';
            $string .= 'jQuery( document ).ready( function() {';
                $string .= '$( \'a#SendEmail_' . $company->id . '\' ).sharelink( { type : \'firms\', id : ' . $company->id . ' } )';
            $string .= '} );';
        $string .= '</script>';
		if($company->CompanyStatus[0]->status_id == 1 && $company->weight)
			$string .= '<div class="company-list-item vip">';
		elseif($company->CompanyStatus[0]->status_id > 1 && $company->weight)
			$string .= '<div class="company-list-item vip-2">';
		else
			$string .= '<div class="company-list-item">';

			$string .= 	'<div class="content">';
			$string .= 	'<div class="index">'.$index.'</div>';
					$string .= 	'<h2><a href="/firm/'.$company->id.'/">'.$company->name.'</a></h2>';
                    if ($admin === true) {
                        $string .= '<a href="/admin/status/add?company_id='.$company->id.'">Редактировать</a>';
                    }
					$string .= 	'<div class="main_info">';
						$string .= 	'<div class="address">';
							$string .= 	$company->Contact[0]->city->name . ', ' . $company->Contact[0]->street . ', ' . $company->Contact[0]->house_num;
							$string .= 	'<a href="/firm/'.$company->id.'/" class="map">';
								$string .= 	'<span class="flag"></span>';
								$string .= 	'<span class="text">на карте</span>';
							$string .= 	'</a>';
						$string .= 	'</div>';
						$string .= 	'<div class="phone">';
							if($company->Contact[0]->Phone[0]->code != ''){
								$string .= 	'(' . $company->Contact[0]->Phone[0]->code . ') ';
							}
							$string .= 	$this->separatePhone($company->Contact[0]->Phone[0]->number);
						$string .= 	'</div>';
						$string .= 	'<div>';
						    if ($company->url){
						        $string .= 	'<span class="site">';
						            $string .= 	'<span class="icon"></span><a href="' . $company->url . '">' . $company->url . '</a>';
						        $string .= 	'</span>';
						    }
						    if ($company->email){
						        $string .= 	'<span class="mail">';
						            $string .= 	'@ <a href="mailto:' . $company->email . '">' . $company->email . '</a>';
						        $string .= 	'</span>';
						    }
						$string .= 	'</div>';
					$string .= 	'</div>';
					$string .= 	'<div class="timetable">';
					switch($company->Contact[0]->status) :
						case "24x7" :
							$string .= 	'Круглосуточно';
							break;
						case "custom" :
							$string .= 	$this->timetable($company->Contact[0]->timetable);
							break;
						case "none" :
						default :
							break;
					endswitch;

					$string .= 	'</div>';
					$string .= 	'<div class="category">';
                        if ($admin === true) {
                            $months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
                            foreach ($company->CompanyStatus as $package) {
                                $date_start = getdate(strtotime($package->date_start));
                                $date_end = getdate(strtotime($package->date_end));
                                $string .= 	'<strong>C ' . $date_start['mday']. ' '.
                                               $months[$date_start['mon']-1] . ' по ' .
                                               $date_end['mday']. ' '.
                                               $months[$date_end['mon']-1] . '</strong>&nbsp;';
                            }
                        }
						$categories_list = array();
						foreach($company->CompanyCategory as $category)
							$categories_list[] = $category->category->title;
						$string .= 	implode(', ', $categories_list);
					$string .= 	'</div>';
					$string .= 	'<div class="clear"></div>';
					$string .= 	'<div class="actions">';
						$string .= 	'<div class="right">';
                            $string .= '<div class="addthis_toolbox addthis_default_style " addthis:url="http://beta.amurspravka.com/firm/'.$company->id.'" addthis:title="'.$company->name.'">';
                                $string .= '<a href="http://www.addthis.com/bookmark.php?v=250&pubid=amurnet" class="addthis_button_compact"></a>';
                                $string .= '<a class="addthis_button_facebook" style="margin-right: 5px;"></a>';
                                $string .= '<a class="addthis_button_twitter" style="margin-right: 5px;"></a>';
                            $string .= '</div>';
//                            $string .= '<script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script>';
//                            $string .= '<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=amurnet"></script>';
							//$string .= 	'<a class="facebook" target="_blank" href=" http://www.facebook.com/share.php?u=http://beta.amurspravka.com/firm/'.$company->id.'&t='.$company->name.'"></a>';
							//$string .= 	'<a class="twitter" href="#"></a>';
						$string .= 	'</div>';
						$string .= 	'<div style="padding-top:2px;">';
							$string .= 	'<a href="#"><span class="icon alert"></span>карточка организации</a>';
							$string .= 	'<a href="#" id="SendEmail_'.$company->id.'"><span class="icon mail"></span>отправить на почту</a>';
                            /*
                             * @todo Creating routers for favorites
                             */
							$string .= 	'<a href="/favorites/index/add/?type=firms&item_id='.$company->id.'"><span class="icon pen"></span>сохранить в избранное</a>';
							$string .= 	'<!-- <a href="#"><span class="icon cloud"></span>распечатать</a> -->';
							$string .= 	'<a href="#" id="SendError_'.$company->id.'"><span class="icon cloud"></span>сообщить об ошибке</a>';
						$string .= 	'</div>';
						$string .= 	'<div class="clear"></div>';
					$string .= 	'</div>';

					if ( ($company->weight) && ($company->logo) ) {
						$string .= 	'<a href="#">';
							$string .= 	'<img class="logo" src="'.$company->logo.'" alt="'.$company->name.'">';
						$string .= 	'</a>';
						$string .= 	'<div class="vip-marker">▸</div>';
					}
				$string .= 	'</div>';
			$string .= 	'</div>';

		return $string;
	}

	protected function timetable($timetable)
	{
		$timetable = array(
			$this->_monday => $timetable->monday,
			$this->_tuesday => $timetable->tuesday,
			$this->_wednesday => $timetable->wednesday,
			$this->_thursday => $timetable->thursday,
			$this->_friday => $timetable->friday,
			$this->_saturday => $timetable->saturday,
			$this->_sunday => $timetable->sunday
		);

		$ret = $this->identDays($timetable);
		$string = '';
		foreach($ret as $key => $value){
			if(!empty($key))
				$string .= $value . ': ' . $this->weekday($key) . '<br />';
		}

		$weekends = $this->weekends($timetable);
		if($weekends){
			if(count($weekends) == 1){
				$string .= 'Выходной: ';
			}else{
				$string .= 'Выходные: ';
			}

			foreach($weekends as $weekend){
				$string .= $weekend . ', ';
			}

			$string = substr($string, 0, -2);
		}

		return $string;
	}

	/**
	 *
	 * @param array $timetable
	 * @return string
	 */
	protected function identDays($timetable)
	{
		$unique_days = array_unique($timetable);

		foreach($unique_days as $day => $time){
			$days_start[$time] = $day;
		}

		foreach($unique_days as $key => $value){
			foreach($timetable as $day => $time){
				if($value == $time){
					$days_end[$time] = $day;
				}
			}
		}

		foreach($days_end as $time => $day){
			if($days_start[$time] != $days_end[$time]){
				$days_start[$time] .= '-' . $days_end[$time];
			}
		}

		return $days_start;
	}

	/**
	 *
	 * @param array $timetable
	 * @return string
	 */
	protected function weekday($timetable)
	{
		if(empty($timetable)){
			return false;
		}else{
			if(strpos($timetable, '#')){
				list($worktime, $break) = explode('#', $timetable);
			}else{
				$worktime = $timetable;
			}

			list($from, $to) = explode('-', $worktime);

			$string = $from . $this->_startend_separator . $to;
			if(isset($break)){
				list($break_from, $break_to) = explode('-', $break);
				$string .=  ' (' . $break_from . $this->_startend_separator . $break_to . ')';
			}

			return str_replace(':', $this->_time_separator, $string);
		}
	}

	/**
	 *
	 * @param array $timetable
	 * @return array|false
	 */
	protected function weekends($timetable)
	{
		$weekends = array();
		foreach($timetable as $day => $time){
			if(empty($time)){
				$weekends[] = $day;
			}
		}

		if(count($weekends)){
			return $weekends;
		}

		return false;
	}

    public function separatePhone($phone)
    {
        if(strlen($phone) == 5){
			$phone = substr($phone, 0, 2).$this->separator.substr($phone, -3);
			$phone = substr($phone, 0, 4).$this->separator.substr($phone, -2);
		}elseif(strlen($phone) == 6){
			$phone = substr($phone, 0, 2).$this->separator.substr($phone, -4);
			$phone = substr($phone, 0, 5).$this->separator.substr($phone, -2);
		}elseif(strlen($phone) == 7){
			$phone = substr($phone, 0, 3).$this->separator.substr($phone, -4);
			$phone = substr($phone, 0, 6).$this->separator.substr($phone, -2);
		}elseif(strlen($phone) == 11){
			$phone = substr($phone, 0, 1).$this->spacer.substr($phone, -10);
			$phone = substr($phone, 0, 5).$this->spacer.substr($phone, -7);
			$phone = substr($phone, 0, 9).$this->separator.substr($phone, -4);
			$phone = substr($phone, 0, 12).$this->separator.substr($phone, -2);
		}

		return $phone;
    }
}
