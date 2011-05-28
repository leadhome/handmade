<?php

class pEngine_Validator_Timetable extends Zend_Validate_Abstract
{
	const MSG_FORMAT = 'msgFormat';
	const MSG_SEP_1 = 'msgSep1';
	const MSG_SEP_2 = 'msgSep2';
	const MSG_NEW_DAY = 'msgNewDay';
	const MSG_DAYS_1 = 'msgDays1';
	const MSG_DAYS_2 = 'msgDays2';
	const MSG_NO_DAYS = 'msgNoDays';

	protected $_messageTemplates = array(
		self::MSG_FORMAT => 'График работы введён в неверном формате, либо содержит опечатки',
		self::MSG_SEP_1 => 'Неверно указаны разделители дней недели',
		self::MSG_SEP_2 => 'Запись не может начинаться или заканчиваться разделителем',
		self::MSG_NEW_DAY => 'Был введён несуществующий день недели',
		self::MSG_DAYS_1 => 'Дни недели указаны без разделителя',
		self::MSG_DAYS_2 => 'Дни недели указаны в неверном порядке',
		self::MSG_NO_DAYS => 'Не указаны дни недели'
	);

	public function isValid($value)
	{
		$this->_setValue($value);

		$days = array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');

		$lines = explode("\n", $value);
		foreach($lines as $line) {
			$pattern = '/(.*?)(?: +(\d{1,2}:\d{2})-(\d{1,2}:\d{2})(?:[^, \d]*?$|, +(перерыв) (\d{1,2}:\d{2})-(\d{1,2}:\d{2}))| +(выходной *$))/';
			preg_match($pattern, $line, $match);
			
			if(!is_array($match) || count($match) == 0) {
				$this->_error(self::MSG_FORMAT);
				return false;
			}

			if(!isset($match[1]) || $match[1] == '') {
				$this->_error(self::MSG_NO_DAYS);
				return false;
			}

			$pattern_days = '/(Пн|Вт|Ср|Чт|Пт|Сб|Вс|-|,)/';
			preg_match_all($pattern_days, $match[1], $match_days);

			$last_sep = ',';
			$last_token_was_sep = true;
			$last_day_ind = -1;
			foreach($match_days[1] as $day) {
				$ind = array_search($day, $days);
				if($ind === false) {
					if($day != '-' && $day != ',') {
						//ERROR
						$this->_error(self::MSG_NEW_DAY);
						return false;
					}

					if($last_token_was_sep) {
						//ERROR
						$this->_error(self::MSG_SEP_1);
						return false;
					}

					if($day == '-' && $last_sep == '-') {
						//ERROR
						$this->_error(self::MSG_SEP_1);
						return false;
					}

					$last_token_was_sep = true;
					$last_sep = $day;
				} else {
					if(!$last_token_was_sep) {
						//ERROR
						$this->_error(self::MSG_DAYS_1);
						return false;
					}

					if($last_sep == '-') {
						if($ind <= $last_day_ind) {
							//ERROR
							$this->_error(self::MSG_DAYS_2);
							return false;
						}
					}
					$last_token_was_sep = false;
					$last_day_ind = $ind;
				}

			}
			if($last_token_was_sep) {
				//ERROR
				$this->_error(self::MSG_SEP_2);
				return false;
			}
		}

		return true;
	}
}
