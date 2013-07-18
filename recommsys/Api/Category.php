<?php
//zhaojun@baixing.com
namespace Api;
class Category extends \Api implements \ApiLimitation, \ApiList {
	public static function apiList() {
		$apiList = array();
		//read
		$apiList['category.list'] = array(
			'require'	=> array(),
			'optional'	=> array('categoryEnglishName', 'fields'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		$apiList['category.filter'] = array(
			'require'	=> array('categoryEnglishName', 'cityEnglishName'),
			'optional'	=> array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		$apiList['category.meta'] = array(
			'require'	=> array('categoryEnglishName'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		return $apiList;
	}

	public static function limitation(\ApiParam $params) {
		return;
	}

	public static function category_list(\ApiParam $params) {
		$category = \Category::loadByName($params->categoryEnglishName ?: 'root');
		$category->clientType = \Category::CLIENT_API;
		$categories = $category->tree();
		$firstCategories = $categories['children'];
		$categories['children'] = array();
		foreach ($firstCategories as $firstCategory) {
			$secondCategories = $firstCategory['children'];
			$firstCategory['children'] = array();
			foreach ($secondCategories as $secondCategory) {
				$firstCategory['children'][] = self::categoryFilter($secondCategory, $params);
			}
			$categories['children'][] = self::categoryFilter($firstCategory, $params);
		}

		if($params->api_key && $params->api_key == "baixing_android"){
			$newCategories = self::categoryFilter($categories, $params);
			$push = array(0 => array('name' => '(^_^)新版android 客户端发布，请访问wap.baixing.com 下载更新！',
				'englishName' => 'push',
				'parentEnglishName' => 'root',
				'level' => '100',
				'shortname' => '推广',
				'children' => array(0 => array('name' => '(^_^)新版android 客户端发布，请访问wap.baixing.com 下载更新！',
				'englishName' => 'subpush',
				'parentEnglishName' => 'push',
				'level' => '101',
				'shortname' => '推广'))));
			$newReturn = array_merge($push, $newCategories['children']);
			$newCategories['children'] = $newReturn;
			return $newCategories;	
		}
		return self::categoryFilter($categories, $params);
	}

	public static function category_filter(\ApiParam $params) {
		$category = \Category::loadByName($params->categoryEnglishName);
		$category->clientType = \Category::CLIENT_API;
		$city = \City::loadByName($params->cityEnglishName);
		if (!$category || !$city) throw new \Exception('need categoryEnglishName and CityEnglishName', 501);
		if (!isset(\Page::$context['city'])) \Page::$context['city'] = $city;
		if (!isset(\Page::$context['category'])) \Page::$context['category'] = $category;
		$metas = $category->filterMetas();
		$filters = array();
		$query = array();
		foreach ($metas as $meta) {
			$filters[] = \AdFilter::metaConfig($meta, $params->{$meta->name}, $city, $category);
			$query[] = \AdFilter::metaQuery($meta, $params->{$meta->name});
		}
		return array('query' => trim(join(' ',array_filter($query))), 'filter' => array_filter($filters));
	}

	public static function category_meta(\ApiParam $params) {
		$metaConfig = array();
		$category = \Category::loadByName($params->categoryEnglishName);
		$category->clientType = \Category::CLIENT_API;
		$city = \City::loadByName($params->cityEnglishName ?: 'shanghai');
		if (!isset(\Page::$context['city'])) \Page::$context['city'] = $city;
		if (!isset(\Page::$context['category'])) \Page::$context['category'] = $category;

		if (!isset($metaConfig['wanted']) && $category->get('wanted')){
			$meta = \Meta::factory('Radio', $category)->set('name', "wanted")->set('displayName', '供求')->set('controlView', 'radio')->set('style', 'required')->set('values', '1,0')->set('labels', explode('|', $category->get('wanted')));
			$metaConfig['wanted'] = self::metaConfig($meta, $city, $category, $params);
		}

		foreach ($category->metas() as $meta) {
			if (empty($meta->name)) continue;
			$metaConfig[$meta->name] = self::metaConfig($meta, $city, $category, $params);
		}

		//todo 暂时兼容
		if (!isset($metaConfig['images'])) {
			$meta = \Meta::factory('Input', $category)->set('name', "images")->set('displayName', '照片')->set('controlView', 'image')->set('style', '');
			$metaConfig['images'] = self::metaConfig($meta, $city, $category, $params);
		}

		return $metaConfig;
	}

	private static function metaConfig($meta, $city, $category, \ApiParam $params) {
		if ($meta instanceof \ObjectMeta) $meta->init(new \Url());
		$meta->autoValues(new \Url(), $city, $category);
		$metaConfig = array(
			'values' => array(),
			'label' => $meta->displayName,
			'view' => $meta->controlView,
			'name' => $meta->name,
			'required' => strpos($meta->style, 'required') !== false,
			'attributes' => $meta->controlAttributes(),
			'level' => $meta->level,
			'filter' => $meta->filter,
		);
		if ($meta instanceof \TreeMeta) {
			foreach ($meta->metas as $eachMeta) {
				foreach ($eachMeta->values() as $eachValue) {
					$valueData['label'] = $meta->label($eachValue);
					$metaConfig['values'][$eachMeta->dataType . '.' . $eachValue] = $valueData;
					$metaConfig['filters'][$eachMeta->dataType . '.' . $eachValue] = $valueData;
				}
			}
		} else {
			foreach ($meta->values() as $eachValue) {
				$valueData['label'] = $meta->label($eachValue);
				$metaConfig['values']["$eachValue"] = $valueData;
				$metaConfig['filters']["$eachValue"] = $valueData;
			}
		}
		return $metaConfig;
	}

	private static $fields;
	private static function categoryFilter($object, \ApiParam $params) {
		if (!self::$fields) {
			$allowFields = array(
				'name', 'englishName', 
				'parentEnglishName', 
				'level',
				'shortname',
				'redirect',
				'children',//default
				'viewType',
				'meta',
			);
			if ($params->fields) self::$fields = array_intersect($allowFields, explode(',', (string)$params->fields));
			else self::$fields = array_slice($allowFields, 0, 9);
		}
		$newObject = array();
		foreach (self::$fields as $field) {
			if (isset($object[$field]) && !empty($object[$field])) $newObject[$field] = $object[$field];
		}
		if (isset($object['appviewtype']) && !empty($object['appviewtype'])) $newObject['viewType'] = $object['appviewtype'];
		if (isset($object['appname']) && !empty($object['appname'])) $newObject['name'] = $object['appname'];
		return $newObject;
	}
}
?>