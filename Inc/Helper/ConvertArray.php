<?php
class Inc_Helper_ConvertArray {
	public function getArray($data,$params = array()) {
		$array = array();
		if(!count($params)) return $data;
		
		$key = -1;
		foreach($data as $value) {
			if(!$params['key']) {
				$key++;
			} else {
				$key = $value[$params['key']];
			}
			if(count($params['fields'])>1) {
				foreach($params['fields'] as $field) {
					$array[$key][$field] = $value[$field];
				}
			} else {
				$array[$key] = $value[$params['fields'][0]];
			}
		}
		return $array;		
	}
}