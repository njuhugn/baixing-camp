<?php
//wangweihua@baixing.com
namespace Api;
class Mobiletoken extends \Api implements \ApiLimitation, \ApiList {
	public static function apiList() {
		return array();
	}

	public static function limitation(\ApiParam $params) {
		if (\Api::getApiUser($params->api_key)->get('API_TYPE') != self::TYPE_SUPER) throw new \Exception('无权限调用此API', 601);
	}

	public static function mobiletoken_update(\ApiParam $params) {
		
		if($params->mobile){
			try {
				$user = User::user_show($params);
				$userId = $user->id;
				$userMobile = $user->mobile;
			} catch (\Exception $e) {
				$userId = '';
				$userMobile = '';
			}
		}
		
		if($params->userId){
			try{
				$user = new \User();
				$user->load($params->userId);
				$userId = $params->userId;
				$userMobile = $user->mobile;
			}catch(\Exception $e){
				$userId = '';
				$userMobile = '';
			}
		}
		
		\NotificationToken::updateDevice($params->appType, $params->appVersion, $params->deviceUniqueIdentifier, $params->deviceToken, $userId, $userMobile);
		return true; 
	}
}
?>