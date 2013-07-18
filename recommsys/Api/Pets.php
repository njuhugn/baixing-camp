<?php
//wangweihua@baixing.com
namespace Api;
class Pets extends \Api implements \ApiLimitation, \ApiList {
	public static function apiList() {
		return array();
	}

	public static function limitation(\ApiParam $params) {
		if (\Api::getApiUser($params->api_key)->get('API_TYPE') != self::TYPE_SUPER) throw new \Exception('无权限调用此API', 601);
	}

	public static function pets_categorylist(\ApiParam $params) {
		return \Config::item('app.petsCategoryList');
	}

	public static function pets_hot(\ApiParam $params) {
		return array(
			array(
				'name'	=> '随手解救流浪动物',
				'url'	=> 'bxpets://post',
				'pic'	=> 'http://www.baixing.com/images/pets/cate_pic_hot.png',
			),
		);
	}

	public static function pets_home(\ApiParam $params) {
		$params->query = (string)new \AndQuery(
				new \Query('cityEnglishName', $params->cityEnglishName),
				new \Query('imageFlag', '1'),
				new \OrQuery(
					new \Query('categoryEnglishName', 'chongwujiaoyi'),
					new \AndQuery(
						new \Query('categoryEnglishName', 'qitachongwu'),
						new \Query('description', '猫')
					)
				)
			);
		return Ad::ad_list($params);
	}
}
?>