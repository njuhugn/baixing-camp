<?php
//wangweihua@baixing.com
namespace Api;
class Pailemai extends \Api implements \ApiLimitation, \ApiList {
	public static function apiList() {
		return array();
	}

	public static function limitation(\ApiParam $params) {
		if (\Api::getApiUser($params->api_key)->get('API_TYPE') != self::TYPE_SUPER) throw new \Exception('无权限调用此API', 601);
	}

	public static function pailemai_categorylist(\ApiParam $params) {//拍了卖专用
		$mobileCategoryList = array();
		$coverImageList = \Config::item('app.categoryCoverImages');
		$blackCategoryList = array('huochepiao', 'shoujihaoma', 'cheliangqiugou', 'qiumai');

		foreach ($coverImageList as $categoryEnglishName => $imageUrl) {
			if (in_array($categoryEnglishName, $blackCategoryList)) continue;
			$category = \Category::loadByName($categoryEnglishName);
			if ($category->name) {
				$mobileCategoryList[] = array(
					'name'			=> $category->name,
					'queryUri'		=> 'categoryEnglishName:' . $categoryEnglishName,
					'pic'			=> $imageUrl,
					'categoryEnglishName'	=> $categoryEnglishName
				);
			}
		}
		return $mobileCategoryList;
	}

}
?>