<?php
//zhaojun@baixing.com
namespace Api;
class City extends \Api implements \ApiLimitation, \ApiList {
	public static function apiList() {
		$apiList = array();
		//read
		$apiList['city.ip2city'] = array(
			'require'	=> array('ip'),
			'optional'	=> array('fields'),
			'return'	=> \ApiList::API_RETURN_OBJECT,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		$apiList['city.mobile2city'] = array(
			'require'	=> array('mobile'),
			'optional'	=> array('fields'),
			'return'	=> \ApiList::API_RETURN_STRING,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		$apiList['city.list'] = array(
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		$apiList['city.area'] = array(
			'require'	=> array('cityEnglishName'),
			'optional'	=> array('fields'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		$apiList['city.loadByName'] = array(
			'require'	=> array('cityEnglishName'),
			'optional'	=> array('fields'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		return $apiList;
	}

	public static function limitation(\ApiParam $params) {
		return;
	}

	public static function city_ip2city(\ApiParam $params) {
		$city = \Ip::ip2city($params->ip, \Ip::INFO_ARRAY);
		if (is_array($city) && isset($city['englishName'])) return self::cityFilter(\City::loadByName($city['englishName']), $params);
		return null;
	}

	public static function city_mobile2city(\ApiParam $params) {
		if (!$params->mobile) throw new \Exception('invalid mobile', 503);
		$city = \City::loadByCName(\MobileArea::city($params->mobile)) ?: new City();
		if ($city) return self::cityFilter($city, $params);
		return null;
	}

	public static function city_list(\ApiParam $params) {
		$ret = array();
		foreach (\City::getCities() as $city) {
			$ret[$city->oid] = self::cityFilter($city, $params);
		}
		return $ret;
	}

	public static function city_area(\ApiParam $params) {
		if (!($city = \City::loadByName($params->cityEnglishName))) throw new \Exception('invalid cityEnglishName', 517);
		return self::areaFilter(\Data::loadObj($city->get('objectId'))->children, $params);
	}

	public static function city_loadByName(\ApiParam $params) {
		if (($city = \City::loadByName($params->cityEnglishName))) {
			$data = self::cityFilter($city, $params);
		} else {
			$data = null;
		}
		return compact('data');
	}

	private static $fields;
	private static function cityFilter($object, \ApiParam $params) {
		if (!self::$fields) {
			$allowFields = array(
				'name', 'englishName', 'sheng',//default
			);
			if ($params->fields) self::$fields = array_intersect($allowFields, explode(',', (string)$params->fields));
			else self::$fields = array_slice($allowFields, 0, 3);
		}
		$newObject = array();
		foreach (self::$fields as $field) {
			if ($object->$field != null) $newObject[$field] = $object->$field;
		}
		return $newObject;
	}

	private static function areaFilter($object, \ApiParam $params) {
		if (is_array($object)) {
			foreach ($object as $key => $item) {
				$object[$key] = self::areaFilter($item, $params);
			}
			return $object;
		}
		if (!is_a($object, 'Data')) return;
		if (!self::$fields) {
			$allowFields = array(
				'id', 'name', 'pinyin' => 'englishName', //default
				'children'
			);
			if ($params->fields) self::$fields = array_intersect($allowFields, explode(',', (string)$params->fields));
			else self::$fields = array_slice($allowFields, 0, 3);
		}
		$newObject = array();
		foreach (self::$fields as $key => $field) {
			if ($field == 'children' && count($object->children)) $newObject['children'] = self::areaFilter($object->children, $params);
			elseif ($value = $object->{is_numeric($key) ? $field : $key}) $newObject[$field] = $value;
		}
		return $newObject;
	}
}
?>