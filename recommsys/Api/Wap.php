<?php
//zhaojun@baixing.com
namespace Api;
/*
 * Wap Api 仅供wap站以静态方法的形式调用，会取cookie
 */
class Wap extends \Api {
	public static function getActionMeta(\ApiParam $params) {
		//
		$category = \Category::loadByName($params->categoryEnglishName);
		$user = \Visitor::user();
		$mobile = $user ? $user->mobile : null;
		$metaData = array();
		$microControls = array();
		$isActive = false;
		return compact('isActive', 'metaData', 'user', 'microControls');
	}
}