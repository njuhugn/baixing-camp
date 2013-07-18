<?php
//zhaojun@baixing.com
namespace Api;
class Data extends \Api implements \ApiLimitation, \ApiList {
	public static function apiList() {
		return array();
	}

	public static function limitation(\ApiParam $params) {
		if (\Api::getApiUser($params->api_key)->get('API_TYPE') != self::TYPE_SUPER) throw new \Exception('无权限调用此API', 601);
	}

	public static function data_create(\ApiParam $params) {
		return self::data_update($params, \MysqlData::SAVE_INSERT);
	}

	public static function data_read(\ApiParam $params) {
		$dataClass = '\\' . $params->dataType;
		$data = new $dataClass;
		$query = new \AndQuery();
		foreach ($params as $key => $value) {
			if (in_array($key, array('dataType', 'limit', 'start', 'api_key', 'timestamp', 'access_token'))) continue;
			$query->add(new \Query($key, $value));
		}
		if ($params->dataType == 'Data') {
			$res = $data->find($params->type, $query, array('limit' => $params->limit));
		} else {
			$res = $data->find($query, array('limit' => $params->limit));
		}
		$dataArray = array();
		foreach ($res as $resData) {
			$dataArray[] = self::parseData($resData);
		}
		return array('data' => $dataArray);
	}

	public static function data_update(\ApiParam $params, $insertFlag = false) {
		$dataClass = '\\' . $params->dataType;
		$data = new $dataClass;
		if ($data instanceof \MysqlData) {
			$key = $data->key();
			if ($params->$key) {
				try {
					$data->load($params->$key);
					if ($insertFlag && $data->$key) return array('response' => false, 'msg' => 'key ' . $params->$key . ' already exist');
				} catch (\Exception $e) {
					if (!$insertFlag) return array('response' => false, 'msg' => 'key ' . $params->$key . ' not exist');
				}
			}
		}
		foreach ($params as $key => $value) {
			if (in_array($key, array('dataType'))) continue;
			if ($params->dataType != 'Data') $data->set($key, $value);
			else $data->$key = $value;
		}
		$data->save($insertFlag);
		return array('response' => true, 'data' => self::parseData($data));
	}

	public static function data_delete(\ApiParam $params) {
		$dataClass = '\\' . $params->dataType;
		$data = new $dataClass;
		foreach ($params as $key => $value) {
			if (in_array($key, array('dataType'))) continue;
			$data->$key = $value;
		}
		if ($params->dataType == 'Data') return array('response' => false, 'msg' => 'can not delete a Data instance');
		try {
			$data->load();
			return array('response' => (bool)$data->delete());
		} catch (\Exception $e) {
			return array('response' => false);
		}
	}

	private static function parseData($data) {
		$newData = array();
		foreach ($data as $k => $v) {
			if (is_string($v)) $newData[$k] = $v;
			if ($k == 'attris' && is_array($v)) $newData = array_merge($newData, $v);
		}
		return $newData;
	}
}
?>