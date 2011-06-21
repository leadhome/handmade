<?php
class Inc_Geo_Ip {
	protected static $instance;
	private $_config = array('dbHost'=>'localhost','dbUser'=>'root','dbPassword'=>'ususus','dbDatabase'=>'geoip');
	public $_infoIp = array('country_id'=>0,'country'=>'','region'=>'','city_id'=>0,'city'=>'');
	
	public static function getInstance()  {
		if (!is_object(self::$instance)) self::$instance = new self;
		return self::$instance;
    }
	public function __construct() {
		$config = $this->_config;
		
		$link = @mysql_connect ($config['dbHost'], $config['dbUser'], $config['dbPassword']);
		if ($link && mysql_select_db ($config['dbDatabase'])) {
			@mysql_query ("set names utf8");
		} else {			
			return $this->setDefault();
		}
	}
	public function setDefault() {
		$infoIp = $this->_infoIp;
		$infoIp['country_id'] = 20;
		$infoIp['country'] = 'Российская Федерация';
		$infoIp['region'] = 'Московская область';
		$infoIp['city_id'] = 23541;
		$infoIp['city'] = 'Москва';
		return $infoIp;
	}
	public function getInfo($ip) {
		$infoIp = $this->_infoIp;
		$ip = "87.226.228.26";
		$int = $this->ip2int($ip);		
		
		// Ищем по российским и украинским городам
		$query = 'select * from (select * from net_ru where begin_ip<='.$int.' order by begin_ip desc limit 1) as t where end_ip>='.$int;
		$result = @mysql_query($query);
		if ($row = @mysql_fetch_array($result)) {
			$infoIp['city_id'] = $row['city_id'];
			
			$query = 'select * from net_city where id="'.$infoIp['city_id'].'"';
			$result = @mysql_query($query);
			if ($row = @mysql_fetch_array($result)) {
				$infoIp['city'] = $row['name_ru'];
				$infoIp['country_id'] = $row['country_id'];
			}
		}
		
		// Название страны
		if ($infoIp['country_id']) {
			$query = 'select * from net_country where id='.$infoIp['country_id'];
			$result = @mysql_query($query);
			if ($row = @mysql_fetch_array($result)) {
				$infoIp['country'] = $row['name_ru'];
			}
		}  else return $this->setDefault();
		
		//Название региона
		if ($infoIp['city_id']) {
			$query = 'select * from net_t_city where link_id='.$infoIp['city_id'];
			$result = @mysql_query($query);
			if ($row = @mysql_fetch_array($result)) {
				$infoIp['region'] = $row['district'];
			}
		} else return $this->setDefault();
		
		return $infoIp;
	}
	// Преобразуем ip в число
	private function ip2int($ip) {
		$part = explode(".", $ip);
		$int = 0;
		if (count($part) == 4) {
			$int = $part[3] + 256 * ($part[2] + 256 * ($part[1] + 256 * $part[0]));
		}
		return $int;
	}
	// Преобразуем число в ip
	function int2ip($int) {
		$w = $int / 16777216 % 256;
		$x = $int / 65536 % 256;
		$y = $int / 256 % 256;
		$z = $int % 256;
		$z = $z < 0 ? $z + 256 : $z;
		return "$w.$x.$y.$z";
	}
}