<?php
//liuweili@baixing.com
//Refactored to be facade of all APIs invoked by mobile clients
//by liuweili@baixing.com, 2012.02.20
//如下函数的错误处理基于内部抛出异常
namespace Api;
require_once HTDOCS_PATH . 'lib/Quota.php';
class Mobile extends \Api implements \ApiLimitation, \ApiList{
	
	public static function apiList() {
		$apiList = array();
		
		
		$apiList['mobile.stats'] = array(//发送用户反馈
			'require'	=> array('api_key','udid'),
			'optional'	=> array('call','sms','weibo','weixin','sixin','vad','addcontact'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE
		);
		
		$apiList['mobile.mobile_config'] = array(
			'require'	=> array('udid'),
			'optional'	=> array(),
			'return'	=> \ApiList::API_RETURN_STRING,
			'type'		=> \ApiList::API_TYPE_READ
		);
		
		$apiList['mobile.trackdata'] = array(
			'require'	=> array('api_key','udid'),
			'optional'	=> array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE
		);
		
		$apiList['mobile.tokenupdate'] = array(//发送用户反馈
			'require'	=> array(),
			'optional'	=> array('appType','appVersion','deviceUniqueIdentifier','deviceToken','userId','mobile'),//duplicated parameters: api_key,version,udid
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE
		);
		
		$apiList['mobile.feedback'] = array(//发送用户反馈
			'require'	=> array(),
			'optional'	=> array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE
		);

		//read
		$apiList['mobile.pushNotification'] = array(//根据当前位置搜索Ad
			'require'	=> array(),
			'optional'	=> array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		
		$apiList['mobile.ad_search'] = array(//搜索Ad
			'require'	=> array('query', 'cityEnglishName'),
			'optional'	=> array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		
		$apiList['mobile.ad_user_list'] = array(//搜索Ad
			'require'	=> array('udid'),
			'optional'	=> array('userId','status','rows', 'start', 'rt'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		
		$apiList['mobile.ad_list'] = array(//搜索Ad
			'require'	=> array(),
			'optional'	=> array('query', 'keyword', 'rows', 'start', 'rt', 'fields','activeOnly'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		$apiList['mobile.ad_nearby'] = array(//根据当前位置搜索Ad
			'require'	=> array('lat', 'lng'),
			'optional'	=> array('query', 'rows', 'start', 'rt', 'd'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		$apiList['mobile.ad_recommend'] = array(
			'require' => array('cityEnglishName'),
			'optional' => array('lat', 'lng'),
			'return' => \ApiList::API_RETURN_ARRAY,
			'type' => \ApiList::API_TYPE_READ,
		);
		
		$apiList['mobile.category_list'] = array(//获取类目tree
			'require'	=> array(),
			'optional'	=> array('categoryEnglishName', 'fields'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		$apiList['mobile.category_meta_filter'] = array(//筛选meta
			'require'	=> array('categoryEnglishName'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		
		$apiList['mobile.category_meta_post'] = array(//发布meta
			'require'	=> array('categoryEnglishName'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		
		
		$apiList['mobile.city_bycellphone'] = array(
			'require'	=> array('mobile'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		$apiList['mobile.city_list'] = array(
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		
		$apiList['mobile.city_hotlist'] = array(
			'require'	=> array('cityEnglishName'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		
		//write
		$apiList['mobile.sendsmscode'] = array(//忘记密码发送短信验证码
			'require'	=> array('mobile'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		$apiList['mobile.resetpassword'] = array(//重置密码
			'require'	=> array('mobile','code','password'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		
		$apiList['mobile.user_login'] = array(//根据手机号码进行登录
			'require'	=> array('mobile','password','nickname'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		
		$apiList['mobile.user_register'] = array(//注册新帐号
			'require'	=> array('mobile','password'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		
		$apiList['mobile.user_profile'] = array(//获取用户个人信息
			'require'	=> array('userId'),
			'optional'	=> array('mobile', 'nickname'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		
		$apiList['mobile.user_profile_update'] = array(//编辑用户个人信息
			'require'	=> array('userId','userToken'),
			'optional'	=> array('mobile', 'password','nickname','image_i','gender','家乡','所在地','具体地点','qq'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		
		
		$apiList['mobile.ad_delete'] = array(//删除Ad
			'require'	=> array('adId', 'mobile', 'userToken'),
			'optional'	=> array('nickname'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		
		$apiList['mobile.ad_undelete'] = array(//恢复Ad
			'require'	=> array('adId', 'mobile', 'userToken'),
			'optional'	=> array('nickname'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		
		$apiList['mobile.ad_update'] = array(//更新Ad
			'require'	=> array('adId','title', 'description', 'contact', 'cityEnglishName', 'categoryEnglishName', 'mobile', 'userToken'),
			'optional'	=> array('lat', 'lng', 'image[]'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		
		$apiList['mobile.ad_add'] = array(//新建Ad
			'require'	=> array('title', 'description', 'contact', 'cityEnglishName', 'categoryEnglishName', 'mobile', 'userToken'),
			'optional'	=> array('adId', 'lat', 'lng', 'image[]'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		$apiList['mobile.ad_refresh'] = array(
			'require'	=> array('adId', 'mobile', 'userToken'),
			'optional'	=> array('pay'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		$apiList['mobile.report'] = array(//举报
			'require'	=> array('adId', 'mobile', 'description', 'userToken'),
			'optional' => array('userId'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		
		
		$apiList['mobile.appeal'] = array(//申诉
			'require'	=> array('adId', 'mobile', 'description', 'userToken'),
			'optional' => array('userId'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		
		$apiList['mobile.report'] = array(
			'require'	=> array('adId', 'mobile', 'description', 'userToken'),
			'optional' => array('userId'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		
		$apiList['mobile.metaobject'] = array(
			'require'	=> array('objIds'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);		

		//return: error{code;message}; hasNew: 0,1; serverVersion; apkUrl;
		$apiList['mobile.check_version'] = array( // just for android
			'require'	=> array(),
			'optional' => array('clientVersion'),
			'return'	=> \ApiList::API_RETURN_ARRAY, 
			'type'		=> \ApiList::API_TYPE_READ,
		);		

		$apiList['mobile.debug'] = array(
			'require'	=> array('type'),
			'optional' 	=> array('udid', 'deviceToken'),
			'return'	=> \ApiList::API_RETURN_ARRAY, 
			'type'		=> \ApiList::API_TYPE_READ,
		);	
		
		$apiList['mobile.checkAccountStatus'] = array(
			'require'	=> array( 'mobile'),
			'optional' => array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,			
		);	
		
		$apiList['mobile.verifyMobile'] = array(
			'require'	=> array( 'mobile', 'verifyCode'),
			'optional' => array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,			
		);		

		$apiList['mobile.getUser'] = array(
			'require'	=> array( 'mobile'),
			'optional' => array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,			
		);	
		
		$apiList['mobile.getAroundAds'] = array(
			'require'	=> array( 'cityEnglishName'),
			'optional' => array('query', 'keyword', 'rows', 'start', 'rt', 'fields','activeOnly'),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,			
		);	
		
		$apiList['mobile.get_quota_info'] = array(
			'require'	=> array( 'userId', 'cityEnglishName', 'categoryEnglishName'),
			'optional' => array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,			
		);	
		
		$apiList['mobile.get_favourites'] = array(
			'require'	=> array( 'userId'),
			'optional' => array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,			
		);	
		
		$apiList['mobile.add_favourites'] = array(
			'require'	=> array( 'adIds', 'mobile', 'userToken'),
			'optional' => array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,			
		);	
		
		$apiList['mobile.remove_favourites'] = array(
			'require'	=> array( 'adId', 'mobile', 'userToken'),
			'optional' => array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,			
		);	
		
		$apiList['mobile.ad_reported'] = array(
			'require' => array('adId', 'mobile'),
			'optional' => array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,			
		);

		return $apiList;				
	}

	public static function limitation(\ApiParam $params) {
		return;
	}

	//改为city.mobile2city，兼容一段时间后去掉，预计2012年6月30日
	public static function mobile_city(\ApiParam $params) {
		self::mobile_ad_counter($params);
		if (!$params->mobile) throw new \Exception('invalid mobile',503);
		return \MobileArea::city($params->mobile);
	}
	
	private static function composeResponse($code, $message, $others = null){
		$result = array('error' => array('code' => $code, 'message' => $message));
		if($others){
			return array_merge($result,$others);
		}
		return $result;
	}
	
	public static function mobile_ad_undelete(\ApiParam $params){
		self::mobile_ad_counter($params);
		throw new \Exception('请下载最新版', 123);
	}
	
	public static function mobile_ad_delete(\ApiParam $params){
		self::mobile_ad_counter($params);
		if(!$params->mobile){
			throw new \Exception('请下载最新版', 123);
		}

		if(!Ad::ad_delete($params)) {
			return self::composeResponse(1,'删除信息失败！');
		}
		return self::composeResponse(0,'删除信息成功。');
	}
	
	public static function mobile_metaobject(\ApiParam $params){
		self::mobile_ad_counter($params);
		$objIds = $params->__get('objIds');
		$objIdArray = explode(',', $objIds);
		//var_dump($objIdArray);
		$results = array();
		foreach($objIdArray as $objId)
		{
			$objId = trim($objId);
			$obj = \Data::loadObj($objId);
			$result = array();
			$result['name'] = $obj->id;
			$result['displayName'] = $obj->name;
			$result['unit'] = '';
			$result['controlType'] = 'select';
			$result['numeric'] = '0';
			$result['required'] = '';
			$hasChild = is_array($obj->children) && count($obj->children) > 0 ? '1' : '0';
			if($hasChild){
				$childs = array_values($obj->children);
				$childId = $childs[0];
				$subObj = \Data::loadObj($childId->id);
				$hasChild = is_array($subObj->children) && count($subObj->children) > 0 ? '1' : '0';
			}
			$result['subMeta'] = $hasChild;
			if(is_array($obj->children)){
				foreach($obj->children as $child){
					$result['values'][] = $child->id;
					$result['labels'][] = str_replace(" ", "", $child->name);
				}
			}
			$results[] = $result;
		}
		return $results;
	}
	
	//申诉
	public static function mobile_appeal(\ApiParam $params) {
		self::mobile_ad_counter($params);
		$reports = Ad::ad_appeal($params);		
		if(isset($reports['feedbackId'])) {
			return self::composeResponse(0,'申诉收到');
		}
		return self::composeResponse(1,isset($reports['msg']) ? $reports['msg'] : '申诉失败！');
	}			
	
	
	//举报
	public static function mobile_report(\ApiParam $params) {
		if($params->adId == null){
			return self::mobile_feedback($params);
		}
		self::mobile_ad_counter($params);
		$mobile = $params->mobile;
		if(empty($mobile)){
			//mobile number required
			throw new \Exception('请登录后再进行举报，谢谢配合！', 999);
		}
		$reports = Ad::ad_report($params);
		if(isset($reports['feedbackId'])) {
			return self::composeResponse(0,'举报成功。');
		}
		return self::composeResponse(1,isset($reports['msg']) ? $reports['msg'] : '举报失败！');
	}
	
	public static function mobile_ad_update(\ApiParam $params){
		self::mobile_ad_counter($params);
		if($params->__get('地区')){//iphone v1.0总传回shanghai作为城市，如果地区不是上海，自动切换到相应城市
			$areaObjId = $params->__get('地区');
			$areaObj = \Data::loadObj($areaObjId);
			if($areaObj){
				$params->cityEnglishName = $areaObj->cityEnglishName;
			}
		}
		
		if($params->cityEnglishName && $params->cityEnglishName == 'chengdou') {
			$params->cityEnglishName = 'chengdu';
		}
		
		if(!$params->cityEnglishName || $params->cityEnglishName == ''){
			if($params->mobile){
				$city = City::city_mobile2city($params);
				$params->cityEnglishName = $city['englishName'];
			}
		}
		
		$params->mutiResponse = 1;//需要详细信息
		if ($params->categoryEnglishName) {
			foreach (Category::category_meta($params) as $meta) {
				$filterMeta = self::metaFormat($meta);
				if ($filterMeta['numeric'] == 1) {
					$metaName = $filterMeta['name'];
					if (!is_null($params->$metaName)) $params->$metaName = doubleval($params->$metaName);
				}
			}
		}

		$result = Ad::ad_update($params);
		$adid = -1;
		$msg = '未知错误';
		if($result['code'] == 0){
			$msg = $result['msg'];
			$adid = $result['adId'];
		}else{
			$msg = $result['msg'] . $result['tips'];
			$adid = $result['adId'];
		} 
		
		if(!$params->adId){
			//add new ad, not edit,then log event
			self::log_ad_add($params, $result['code'] == 0? 'succ' :'error' , $adid);
			$params->api = 'mobile.ad_add';
		}else{
			$params->api = 'mobile.ad_update';//hack: force api key changed from ad_add to ad_update to let hereafter logging correct
		}
		
		$contactIsRegisteredUser = false;
		if($params->mobile == null && $params->contact != null){
			$user = \User::loadByMobile($params->contact);
			if($user){
				$contactIsRegisteredUser = true;
			}
		}
		return self::composeResponse($result['code'], $msg, array('id' => $adid, 'contactIsRegisteredUser' => $contactIsRegisteredUser));
	}
	
	private static function loadAndCheck($adId, $user = null, $checkStatus = true) {
		return Ad::loadAndCheck($adId,$user,$checkStatus);
	}

	public static function mobile_ad_refresh(\ApiParam $params){	//这个需要和Ad.php里面的ad_refresh合起来！ @todo yubing
		self::mobile_ad_counter($params);
		if(!$params->mobile){
			throw new \Exception('请下载最新版', 123);
		}
		$user = User::user_show($params);
		$ad = Ad::loadAndCheck($params->adId, $user, true, $params->udid);
		//1.刷新之前弹出alert框，询问用户“刷新可以让该信息重回顶部，您本月还能刷新该信息 N 次。” (N为用户当前在该类目的刷新限额)

		//确定刷新之后提示“刷新成功”。
		if($params->pay && $params->pay == '1'){//借用这个pay 参数来作为用户确认刷新（兼容老客户端）
			if($ad->quotaRefresh()){
				\AdLogger::log($user, $ad, null, null, '手机端刷新', \AdLogger::TYPE_REFRESH);
				return self::composeResponse(0, '刷新成功');
			}else{
				return self::composeResponse(1, '免费刷新额度已经用完，手机端暂不支持余额刷新。');
			}
		}else{//提示用户刷新额度
			$quota = new \RefreshQuota($ad->userId, $ad->categoryEnglishName, $ad->cityEnglishName());
			if($quota->unused() > 0){
				$message = '刷新可以让该信息重回顶部，您本月还能刷新该信息' . $quota->unused() . '次';
				return self::composeResponse(2, $message);//2 -> 对话框提示
			}else{
				return self::composeResponse(1, '无法刷新！本月免费刷新额度已经用完，请下月再试。');
			}
		}
	}
	
	/**
	 * Before add an Ad, image shall be uploaded via /image_upload first
	 * */
	public static function mobile_ad_add(\ApiParam $params){
		if(!$params->mobile){
			throw new \Exception('请下载最新版', 123);
		}
		self::mobile_ad_counter($params);
		
		return self::mobile_ad_update($params);
	}
	private static function metaMap($categoryName){//名字 => Label
		$mapping = array();
		$category = \Category::loadByName($categoryName);
		$metas = $category->metas();
		foreach ($metas as $meta) {
			if(!$meta->name) continue;
			$mapping[$meta->name] = self::deleteSpecialCharacter($meta->displayName)?$meta->displayName:$meta->name;
		}
		return $mapping;
	}
	private static function metaMassage($meta){//为了兼容目前手机端喜欢的格式
		$metaMap = self::metaMap($meta['categoryEnglishName']);
		$displayableMeta = array();
		foreach($meta as $key => $value){
			if(is_array($value)){
				if(array_key_exists('label',$value)){//供客户端进行显示
					if($metaMap[$key] == '上传照片') continue;
					$displayableMeta[] = $metaMap[$key] . ' ' . $value['label'];//用空格分隔
					$meta[$key] = $value['value'];//把此项转换成非数组value 
				}
			}
		}
		
		if(isset($meta['image'])){//此属性手机app未使用
			unset($meta['image']);
		}
		
		if(isset($meta['attris'])){//此属性手机app未使用
			unset($meta['attris']);
		}
		
		if(isset($meta['categoryNames'])){
			$displayableMeta[] = '分类' . ' ' . strtr($meta['categoryNames'], ',', '|');
		}
		if(isset($meta['contact']) && !\Validate::isMobile($meta['contact'])){
			$displayableMeta[] = '联系方式' . ' ' . $meta['contact'];
		}
		if(isset($meta['count'])){
			$displayableMeta[] = '查看' . ' ' . $meta['count'] . '次';
		}
		
		if(isset($meta['postMethod'])){
			$app = null;
			if($meta['postMethod'] == 'api_mobile_android'){
				$app = 'Android客户端';
			}else if($meta['postMethod'] == 'baixing_ios' || $meta['postMethod'] == 'api_iosbaixing'){
				$app = 'iPhone客户端';
			}else if($meta['postMethod'] == 'api_wap'){
				$app = '手机WAP站';
			}
			if($app){
				$displayableMeta[] = '来自' . ' ' . $app;
			}
		}
		$meta['metaData'] = $displayableMeta;
		return $meta;
	}
	
	private static function convertAdList($ads){
		foreach($ads['data'] as $key => $ad){
			$ads['data'][$key] = self::metaMassage($ad);
		}
		if(is_array($ads['data'])){
			$ads['data'] = array_values($ads['data']);
			$ads['num'] = count($ads['data']);
		}
		
		return $ads;
	}
	
	//帖：0. 正常显示 1. 未通过审核 2. 已删除
	public static function mobile_ad_user_list(\ApiParam $params){
		$params->activeOnly = 0;//查询所有状态的ad
		$params->valueFlag = 1;//手机客户端需要数据进行显示和编辑
		$params->fields = 'count';//广告查看次数
		$ads = Ad::ad_user_list($params);
		return self::convertAdList($ads);
	}
	
	
	public static function mobile_ad_list(\ApiParam $params){
		self::mobile_ad_counter($params);
		
		if($params->api_key == 'api_mobile_android' && $params->version == '2.0.1'){
			\InstantCounter::count('android_listing');
		}
		if(!$params->exists('activeOnly')) $params->activeOnly = 1;//没有参数，则默认返回active ad，保证兼容
		if(!$params->valueFlag) $params->valueFlag = 1;//手机客户端需要数据进行显示和编辑
		if(!$params->exists('wanted')) $params->wanted = 0; //手机客户端默认返回转让的信息
		if($params->fields) $params->fields = null;//暂时disable 用户选择返回字段
		$params->fields = 'count';//广告查看次数
		if($params->query) $params->query = preg_replace('/cityEnglishName:chengdou/', 'cityEnglishName:chengdu', $params->query);//补丁，客户端把成都误写成chengdou
		if($params->titleKeyword) unset($params['titleKeyword']);
		if ($params->nearby) {
			\InstantCounter::count('listing_nearby_' . $params->api_key);
			$params->nearby = null;
			$ads = Ad::ad_nearby($params);
		} else {
			\InstantCounter::count('listing_other_' . $params->api_key);
			$ads = Ad::ad_list($params);
		}
		
		$ads = self::convertAdList($ads);
		self::prepare_log_ad_list($params,'succ');
		return $ads;
	}
	
	/**
	 *	Search ad around location 
	 */
	public static function mobile_ad_nearby(\ApiParam $params){
		$params->nearby = 'true';
		return self::mobile_ad_list($params);
	}

	public static function mobile_ad_recommend(\ApiParam $params) {
		if(!$params->exists('activeOnly')) $params->activeOnly = 1;//没有参数，则默认返回active ad，保证兼容
		if(!$params->valueFlag) $params->valueFlag = 1;//手机客户端需要数据进行显示和编辑
		if(!$params->exists('wanted')) $params->wanted = 0; //手机客户端默认返回转让的信息
		if($params->fields) $params->fields = null;//暂时disable 用户选择返回字段
		$params->fields = 'count';//广告查看次数
		if($params->query) $params->query = preg_replace('/cityEnglishName:chengdou/', 'cityEnglishName:chengdu', $params->query);//补丁，客户端把成都误写成chengdou
		if($params->titleKeyword) unset($params['titleKeyword']);
		$ads = Ad::ad_recommend($params);
		$ads = self::convertAdList($ads);
		self::prepare_log_ad_list($params,'succ');
		return $ads;
	}
	
	
	private static function loadCategoryName($categoryEnglishName){
		$category = \Category::loadByName($categoryEnglishName);
		if($category) return $category->name;
		return $categoryEnglishName;
	}
	/**
	 *	Search ad by keyword and category
	 */
	public static function mobile_ad_search(\ApiParam $params){
		$result = array();
		$keyword = $params->query;//assume query == keyword
		$resultByFacet = array();
		$options = array(
			'facet.field' => 'categoryEnglishName',
			'facet.mincount' => 0,
			'facet.limit' => 10,
			'waitSuccess' => true
		);
		$q = new \Query('title', $keyword);
		$q = new \AndQuery($q, new \Query('cityEnglishName', $params->cityEnglishName));

		$res = new \ActiveSearcher($q, $options);
		$resultByFacets = $res->facet();
		$result['count'] = count($resultByFacets);
		$data = array();
		foreach(array_keys($resultByFacets) as $categoryEnglishName){
			$matched = $resultByFacets[$categoryEnglishName];
			if ($matched == 0) continue;
			try{
				$categoryName = self::loadCategoryName($categoryEnglishName);
			}catch(\Exception $e){
				continue;
			}
			if(!empty($categoryName)){//中文类目名为空则标志此类目当前不显示
				$data[] = array($categoryEnglishName,$categoryName,$matched);	
			}
		}
		$result['data'] = $data;
		return $result;
	}
	
	public static function mobile_pushNotification(\ApiParam $params){
		return null;
		$isOldVersion = in_array($params->version, array('2.5', '2.5.2', '2.5.1', '2.3'));
		if($isOldVersion){
			if($params->time == null || $params->time == '' || $params->time != '2012/8/15'){
				$newUpdate = array('time' => '2012/8/15',
					'ticket' =>"来自百姓网的问候",
					'title' => '百姓网客户端有新版本了！',
					'content' => '新增周边搜索，离线使用真给力！');
				return $newUpdate;
			}
		}else{
			$mktAndroidPush = MktConfig::loadByConfigKey('mktAndroidPush');
			$dataArray = null;
			if ($mktAndroidPush->id) {//配置存在
				$dataArray = (array) json_decode($mktAndroidPush);
				$pushCode = $dataArray['pushCode'];
				$vop_android = $dataArray['vop_android'];
				$version_android = $dataArray['version_android'];
			}
			//配置不存在则说明没有群发所有安卓用户，则不需要拉push
			if (!isset($dataArray) || !isset($pushCode) || !isset($vop_android)) return;
			if(($params->pushCode == null || $params->pushCode == '' || (int)$params->pushCode < $pushCode)
					&& ($vop_android == '' /*all android*/|| self::androidPullFilter($vop_android, $version_android, $params->version)/*按版本*/)){
				$newUpdate = array('pushCode' => $pushCode,
					'ticket' => $dataArray['ticket'], //"来自百姓网的问候"
					'title' => $dataArray['title'],//'百姓网客户端有新版本了！'
					'content' => $dataArray['content']);//'私信拉！真给力！'
				return $newUpdate;
			}
		}

	}
	
	private static function androidPullFilter($vop_android, $version_android, $version) {
		$involved = false;
		foreach (explode(',', $version_android) as $item)
			if ($version == $item) {
				$involved = true;
				break;
			}
		if ($vop_android == '!=')	$involved = !$involved;
		return $involved;
	}
	
	private static function categoryConfig ($category) {
		if (isset($category['children'])) {
			foreach ($category['children'] as $key => $childCategory)
				$category['children'][$key] = self::categoryConfig($childCategory);
		}
		return $category;
	}
	/*
	 * Fetch category list
	 */
	public static function mobile_category_list(\ApiParam $params){
		self::mobile_ad_counter($params);
		return self::categoryConfig(Category::category_list($params));
	}

	/*
	 * Meta for filter of searching
	 */
	public static function mobile_category_meta_filter(\ApiParam $params){
		self::mobile_ad_counter($params);
		if($params->cityEnglishName && $params->cityEnglishName == 'chengdou') {
			$params->cityEnglishName = 'chengdu';
		}
		$metas = Category::category_meta($params);
		$filterMetas = array();
		foreach (\Page::$context['category']->filterMetas() as $meta) {
			$filterMetas[] = self::metaFormat($metas[$meta->name], TRUE);
		}
		if(isset($metas['wanted'])){
			$filterMetas[] = self::metaFormat($metas['wanted'], TRUE);
		}
		return $filterMetas;
	}
		
	/*
	 * Meta for post Ad
	 */
	public static function mobile_category_meta_post(\ApiParam $params){
		self::mobile_ad_counter($params);
		if($params->cityEnglishName && $params->cityEnglishName == 'chengdou') {
			$params->cityEnglishName = 'chengdu';
		}
		$filterMetas = array();
		$firstMeta = null;
		foreach (Category::category_meta($params) as $meta) {
			$formatMeta = self::metaFormat($meta);
			//todo 暂时兼容 
			// 为兼容旧版本iOS，不要去除description！ -zengming
			if ($formatMeta['name'] == 'image' 
				|| $formatMeta['displayName'] == '上传照片' 
				|| (
					$formatMeta['name'] != 'images' 
					&& $formatMeta['name'] != 'description'
					&& $formatMeta['name'] != 'contact' 
					&& $formatMeta['name'] != '具体地点' 
					&& $formatMeta['required'] != 'required')) {
				continue;
			}
			
			// KTV/酒吧 不能上传图片
			if ($params->categoryEnglishName && $params->categoryEnglishName == 'ktvjiuba' &&
					$formatMeta['name'] == 'images') {
				continue;
			}
			
			$filterMetas[] = $formatMeta;
		}

		if($firstMeta){
			array_unshift($filterMetas, $firstMeta);
		}
		return $filterMetas;
	}	

	/* params: clientVersion
	 * return: error{code;message}; hasNew; serverVersion; apkUrl;
	 */
	public static function mobile_check_version(\ApiParam $params) {
		//clientVersion 可选，不传则为0
		$clientVersion = $params->clientVersion ?: '0'; 
		$versions = \Mobile_Logic::androidVersions();
		$lastVersion = array_shift($versions) ?: array();
		
		if (empty($lastVersion['serverVersion'])) {
			return self::composeResponse(-1, "检查更新接口异常，请稍候再试");
		}
		$serverVersion = $lastVersion['serverVersion'];

		if ( !preg_match("/^[\d\.]+$/", $clientVersion) ) {
			return self::composeResponse(-1, "请输入正确的版本号");
		}
		$hasNew = (version_compare($clientVersion, $serverVersion) < 0 );
		return self::composeResponse(
			0, 
			"百姓网客户端有新版本了！",
			array_merge(
				array(
					'hasNew' => $hasNew,
					'clientVersion' => $clientVersion
					), 
				$lastVersion)
		);
	}

	private static function controlType($metaViewType){
		$controlType = $metaViewType;
		switch(strtolower($metaViewType)){
			case 'select':
			case 'tagselect':
			case 'range':
			case 'radio':
			case 'tagradio':
			case 'tree':
			case 'tableselect':
				$controlType = 'select';//mapping radio and tree to select
				break;
			case 'textarea':	
				$controlType = 'textarea';
				break;
			case 'tagcheckbox':
			case 'checkbox':
				$controlType = 'checkbox';
				break;
			case 'image':
				$controlType = 'image';
				break;
			default://不认识的类型都input
				//$controlType = 'input';
				break;
		}
		return $controlType;
	}
	private static function deleteSpecialCharacter($str){
		return trim(str_replace("\xE3\x80\x80",'',$str));//英文空格，中文远角空格；将来可能需要替换更多特殊字符
	}
	private static function metaFormat($meta, $isFilter = FALSE) {
		$formatMeta = array ( 
			'name' => $meta['name'],
			'displayName' => (self::deleteSpecialCharacter($meta['label']) ?$meta['label']:$meta['name']),//假设显示名称最少两个字
			'unit' => isset($meta['attributes']['unit']) ? $meta['attributes']['unit'] : '',
			'controlType' => self::controlType($meta['view']),
			'numeric' => isset($meta['attributes']['numeric']) ? "{$meta['attributes']['numeric']}" : '0',
			//'default' => '0',//暂时meta不支持,默认第一个value或为空
			'required' => $meta['required'] ? 'required' : '',
												'subMeta' => $meta['view'] == 'tree' ? '1' : '0',
			'level' => $meta['level'],
			'filter' => $meta['filter'],
			//-1表示不支持maxlength
			'maxlength' => isset($meta['attributes']['maxlength']) ? "{$meta['attributes']['maxlength']}" : "-1",
		);
		$values = $labels = array();
		$key_values = $isFilter? 'filters' : 'values';
		if(isset($meta[$key_values]) && (!is_array($meta[$key_values]) || !$meta[$key_values])){ 
			 //没有value的select 转换成input
			 if($formatMeta['controlType'] == 'select'){
				$formatMeta['controlType'] = 'input';
			}
			return $formatMeta;
		}
		if(isset($meta[$key_values]))
		{
			foreach ($meta[$key_values] as $value => $valueConfig) {
				$val = $value;
				if(preg_match('/^.*\.m\d+$/', $value)){//只有符合xxxx.m5678
					$val = end(explode('.', $value));
				}
				if(strlen($val) <=0) continue;			
				$values[] = $val; //目前object value 被"." 分隔，取最后一个元素
				$labels[] = str_replace(" ", "", $valueConfig['label'] ? $valueConfig['label'] : $val);
			}
		}
		if($values){
			$formatMeta['values'] = $values;
			$formatMeta['labels'] = $labels;
			//$formatMeta['default'] = "{$values[0]}";//first value as default,将来需要支持默认值
			if($isFilter){ //hack：如果符合m####,修改name 为 name_s; 如果符合[ * TO * ]修改name为 name_i,否则筛选结果不完整
				$firstvalue = $values[0];
				if($meta['view'] == 'tree' && preg_match('/^m\d+$/', $firstvalue)){
									
					$formatMeta['name'] .= '_s';
				}else if (preg_match('/\[([^\s]+) TO ([^\s]+)\]/', $firstvalue)){
					$formatMeta['name'] .= '_i';
				}
				$formatMeta['controlType'] = 'select';//强制转换select类型
			}
			
		}else{
			//没有value的select 转换成input
			 if($formatMeta['controlType'] == 'select'){
				$formatMeta['controlType'] = 'input';
			}
		}

		$formatMeta = self::addDefaultValues($formatMeta);
		
		return $formatMeta;
	}

	private static function addDefaultValues($formatMeta, $defaults = null) {
		if ($defaults == null)
			$defaults = array('faburen' => 'm33660', 'wanted' => 0);
		
		if (isset($formatMeta['values'])){
			foreach ($defaults as $name => $value) {
				if ($formatMeta['name'] == $name) {
					if (array_search($value, $formatMeta['values']) !== FALSE) {
						$formatMeta['default'] = $value;
					} else {
						$formatMeta['default'] = $formatMeta['values'][0];
					}
				}
			}
		}
		
		return $formatMeta;
	}
	
	/*
	 * Match city by user cell phone number
	 */
	public static function mobile_city_bycellphone(\ApiParam $params){
		self::mobile_ad_counter($params);
		return self::composeResponse(0, '定位城市成功', array('city' => City::city_mobile2city($params)));
	}	
	
	public static function mobile_city_list(\ApiParam $params){
		self::mobile_ad_counter($params);
		return City::city_list($params);
	}
	
	
	
	/**
	 * 之前跟 limei 做推广时对账用的，暂时已不再使用。
	 */
	private static function checkActivation(\ApiParam $params){
		//check if udid in list of download from ad vendor,e.g. limei
		if($params->api_key != 'baixing_ios') return;
		if(!$params->udid) return;
		$udid_hash = new \Redis\Hash('ios_udid_limei', \Redis\Hash::EXPIRE_30DAYS);
		if(!$udid_hash) return;
		if($udid_hash->hasKey($params->udid)){
			$raw_udid = $udid_hash[$params->udid];
			$udid_hash->delKey($params->udid);
			$response = \Http::getUrl('http://api.lmmob.com/capCallbackApi/1/?appId=app_baixing_ios&returnFormat=1&udid=' . $raw_udid);
			if($response == FALSE){
				\Logger::debug('ios_udid_limei_activated_pending',$raw_udid,null,true);
			}else{
				$res = (array)json_decode($response);
				if($res && isset($res['success'])){
					$error = $res['success'];
					if($error == true){
						\Logger::debug('ios_udid_limei_activated_OK',$raw_udid,null,true);
						\InstantCounter::count('ios_udid_limei_activated_OK');
					}else{
						\Logger::debug('ios_udid_limei_activated_KO',$raw_udid,null,true);
						\InstantCounter::count('ios_udid_limei_activated_KO');
					}
				}
			}
	
		}
	}

	public static function mobile_mobile_config(\ApiParam $params){//app mobile tracking config
		$apiKey = $params->api_key;
		//todo udid 区分限制

		//load config data		
		$mktConfig = \MktConfig::loadByConfigKey("mktTrackMobileData");
		$configValue = $mktConfig->configValue;
		$values = json_decode($configValue, true);

		$configArray = array();
		if (isset($values['all'])) {
			$configArray = $values['all'];
		}
		

		if ($apiKey == "api_mobile_android" && isset($values['android'])) {
			$configArray = array_merge($configArray, $values['android']);

			//从配置中加载最新版本
			$versions = \Mobile_Logic::androidVersions();
			$lastVersion = array_shift(array_keys($versions));
			if ($lastVersion) {
				$configArray['serverVersion'] = $lastVersion;
			}
		}else if (isset($values['iphone'])){
			$configArray = array_merge($configArray, $values['iphone']);
		}

		// 加载图片服务器配置
		$img_space = \Config::item("env.img.upyun");
		$img_space_name = $img_space['policy']['bucket'];
		$img_space_policy = base64_encode(json_encode($img_space['policy'] + array("expiration" => time() + 3600*24)));
		$img_space_sign = md5("{$img_space_policy}&{$img_space['form_api_secret']}");

		$imgConfig = array();
		$imgConfig['server'] = "http://v0.api.upyun.com/{$img_space_name}";
		$imgConfig['fileKey'] = 'file';
		$imgConfig['returnKey'] = 'url';

		$imgConfig['params'] = array('policy'=>$img_space_policy,
									 'signature'=>$img_space_sign);

		$configArray['imageConfig'] = $imgConfig;


		return $configArray;
	}
	
	public static function mobile_city_hotlist(\ApiParam $params){//app热点配置
		self::mobile_ad_counter($params);

		$apiKey = $params->api_key;
		$version = $params->version;

		// 2.6前的版本 通过 v 区分返回，后续不需要
		if ( empty($params['v']) && version_compare($version, "2.6") == -1) {
			return self::oldVersionConfig();
		}

		if ( $apiKey == "api_mobile_android" 
			&& version_compare($version, "2.5.2") == -1) {
			return self::oldVersionConfig("_v2");
		}
		
		$mktConfig = \MktConfig::loadByConfigKey('mktAppHotLinks');
		$configValue = $mktConfig->configValue;
		if(empty($configValue)){
			return self::oldVersionConfig("_v2");
		}
		$values = json_decode($mktConfig->configValue, true);
		$subVaules = array();
		if ($apiKey == "baixing_ios" || $apiKey == "api_iosbaixing") {
			$subVaules = isset($values['iphone']) ? $values['iphone'] : null; 
		} else if ($apiKey == "api_mobile_android") {
			$subVaules = isset($values['android']) ? $values['android'] : null; 
		}

		$rootAll = isset($values['all']) ? $values['all'] : array();
		$subAll = isset($subVaules['all']) ? $subVaules['all'] : array();
		$subVer = isset($subVaules[$version]) ? $subVaules[$version] : array();

		$resultConfig = array_merge($rootAll, $subAll, $subVer);
		if (empty($resultConfig)) {
			return self::oldVersionConfig("_v2");
		}
		return $resultConfig;
	}

	/**
	 * 热点配置 hardcode 的默认设置，兼容旧版本
	 */
	private static function oldVersionConfig($v = "default") {
		if ($v == "_v2") {
			$json = "[{\"imgUrl\":\"http://static.baixing.net/images/mobile/mobile_huodong_daqiu_v2.jpg\"," 
			. "\"type\":\"2\",\"data\":{\"keyword\":\"daqiu\",\"title\":\"运动打球\"}}," 
			. "{\"imgUrl\":\"http://static.baixing.net/images/mobile/mobile_ershou_yundongqicai_v2.jpg\"," 
			. "\"type\":\"2\",\"data\":{\"keyword\":\"yundongqicai\",\"title\":\"运动器材\"}}," 
			. "{\"imgUrl\":\"http://static.baixing.net/images/mobile/mobile_huodong_nzn_v2.jpg\"," 
			. "\"type\":\"2\",\"data\":{\"keyword\":\"nvzhaonan\",\"title\":\"女找男\"}}," 
			. "{\"imgUrl\":\"http://static.baixing.net/images/mobile/mobile_ershou_shouji_v2.jpg\"," 
			. "\"type\":\"2\",\"data\":{\"keyword\":\"shouji\",\"title\":\"二手手机\"}}]";
			
			return json_decode($json);
		}
		$json = "[{\"imgUrl\":\"http://static.baixing.net/images/mobile/mobile_ershou_apple.png\"," 
			. "\"type\":\"0\",\"data\":{\"keyword\":" 
			. "\"((categoryEnglishName:shouji AND 手机型号:m35545) OR " 
			. "(categoryEnglishName:bijiben AND 分类:m33550) OR " 
			. "(categoryEnglishName:pingbandiannao AND 分类:m37019) OR " 
			. "(categoryEnglishName:shuma AND 分类:m18872))\",\"title\":\"苹果专区\"}}," 
			. "{\"imgUrl\":\"http://static.baixing.net/images/mobile/mobile_ershou_qiche.jpg\"," 
			. "\"type\":\"0\",\"data\":{\"keyword\":\"categoryEnglishName:ershouqiche\"," 
			. "\"title\":\"二手车\"}}]";
		return json_decode($json);
	}
	
	//register by udid and marked the user type by 
	public static function mobile_user_autoregister(\ApiParam $params){
		$user = new \User();
		$user->id = 'updateNewVersion';
		return self::composeResponse(0, '匿名用户注册成功', array('udid' => $params->udid, 'user' => $user));
	}
	
	private static function doUserRegister(\ApiParam $params) {
		try {
			$user = null;
			$userId = null;
			if (!$params->isRegister) {
				if (!$params->password) throw new \Exception("无效的帐号或密码", 503);

				// 用户输入的帐号可能是nickname或者mobile
				if ($params->nickname) {
					$user = \User::getUserByNick($params->nickname);
					if (!$user) $params->nickname = null;
					else $params->mobile = null;
				}
				if (!$user && $params->mobile) $user = \User::loadByMobile($params->mobile);

				// 用户不存在 或者 密码不对
				if (!$user || !$user->isMatchedPassword($params->password) || $user->isFrozen()) {
					throw new \Exception("无效的帐号或密码", 503);
				}

				// 未绑定手机号码
				if(!$user->mobile || $user->mobile == "")	throw new \Exception("请到百姓网站绑定手机号码", 503);
			} else {
				$userId = User::user_register($params);
			}

			if ($userId && !$user) {
				$user = new \User();
				$user = $user->load($userId);
			}
			if ($user) return array('userId' => $user->id, 'nickname' => $user->nickname, 'mobile' => $user->mobile);
		}catch (\Exception $e) {
			throw $e;
		}
	}
	
	public static function mobile_user_register(\ApiParam $params){
		self::mobile_ad_counter($params);
		$user = \User::loadByMobile($params->mobile);
		if($user != null){
			return self::composeResponse(1, '该手机号已注册');
		}
		
		$params->isRegister = 1;
		$userid_array = self::doUserRegister($params);
		if($params->version && version_compare($params->version,'2.6') >=0){//2.6以上客户端支持XMPP协议
			$params->userId = $userid_array['userId'];
			self::update_xmpp_push_token($params);	
		}
		$user = self::readUserProfile($userid_array['userId']);
		return self::composeResponse(0, '用户注册成功', array('id' => $userid_array, 'user' => $user));
	}
	
	private static function update_xmpp_push_token(\ApiParam $params){
		$params->appType = $params->appType? : $params->api_key;//兼容老协议参数冗余
		$params->appVersion = $params->appVersion? : $params->version;//兼容老协议参数冗余
		$params->deviceUniqueIdentifier = $params->deviceUniqueIdentifier ? : $params->udid;//xmpp push，兼容老协议参数冗余
		
		if(!$params->appType || !$params->appVersion || !$params->deviceUniqueIdentifier){
			return null;//not throw exception 
		}
		//其他可选参数: userId,mobile
		return Mobiletoken::mobiletoken_update($params);
	}
	
	public static function mobile_user_login(\ApiParam $params){
		self::mobile_ad_counter($params);
		$userid_array = self::doUserRegister($params);
		if($params->version && version_compare($params->version,'2.6') >=0){//2.6以上客户端支持XMPP协议
			$params->userId = $userid_array['userId'];
			self::update_xmpp_push_token($params);	
		}
		
		$user = self::readUserProfile($userid_array['userId']);
		return self::composeResponse(0, '用户登录成功', array('id' => $userid_array, 'user' => $user));	
	}
	
	private static function area2City($areaObjId){
		$area = \Data::loadObj($areaObjId);
		if(!$area) return $areaObjId;
		while($area && $area->type != 'city'){
			$area = $area->parent;
		}
		if($area){
			return $area->id;
		}else{
			return $areaObjId;
		}
	}
	
	private static function userObjectToArray(\User $user, $isPublic = TRUE)
	{	
		$result = array();
		$basicAttributes = array('id', 'mobile', 'nickname','gender','家乡','所在地','具体地点','qq','createdTime','description','type');
		foreach ($basicAttributes as $attri) {
			$val = $user->get($attri);
			$result[$attri] = $val !== false ? "{$val}" : "";
		}
		
		if(isset($result['所在地'])){
			$result['所在地'] = self::area2City($result['所在地']);
		}
		
		foreach ($result as $key => $val) {
			if (preg_match('/^m\d+$/', $val)){
				$m = \Data::loadObj($val);
				if ($m) {
					$name = $m->displayName ?: $m->name;
					if ($name) {
						$result[$key .'_name'] = $name;
					}
				}
			}
		}
		
		//user photo
		$result['images'] = array();
		$result['images']['square'] = $user->image(\User::IMAGE_SIZE_SQUARE) ?: '';
		$result['images']['small'] = $user->image(\User::IMAGE_SIZE_SMALL) ?: '';
		$result['images']['big'] = $user->image(\User::IMAGE_SIZE_BIG) ?: '';
		$result['images']['resize180'] = $user->image(\User::IMAGE_RESIZE_180x180) ?: '';
		$result['images']['resize140'] = $user->image(\User::IMAGE_RESIZE_140x140) ?: '';
		$result['images'] = json_decode(preg_replace('/img\d*\.baixing\.net/', 'tu.baixing.net', json_encode($result['images'])), true);
		
		return $result;
	}
	
	private static function readUserProfile($userId){

		$user = new \User();
		if ($userId) {
			try{
				$user = $user->load($userId);
			}catch(\Exception $e){
				return array();
			}
			if(!$user) return array();
		}

		return self::userObjectToArray($user);
	}
	
	
	private static function updateUserProfile($userId, array $attributes){
		$user = new \User();
		if ($userId) {
			$user = $user->load($userId);
			if(!$user) throw new \Exception('userId 无效',9001);
		}
		//昵称
		if(isset($attributes['nickname']) && $attributes['nickname']!=null){
			$nickname = $attributes['nickname'];
			if ($user->get('nickname') != $nickname) {
				$valid = \User::isValidNickname($nickname);
				if ($valid != 'Valid') throw new \Exception($valid, 9002);
				if (!\User::isNicknameAvailable($nickname)) throw new \Exception('该用户名已被注册，请尝试其他用户名。', 9003);
				$user->set('nickname', $nickname);
			}
		}
		//基本属性
		$basicAttributes = array('gender','家乡','所在地','具体地点','qq','description');
		foreach ($basicAttributes as $attri)
		{
			if(isset($attributes[$attri]) && $attributes[$attri] != null){
				$user->set($attri,$attributes[$attri]);	
			}
		}
		//user photo 照片
		if(isset($attributes['image_i'])){
			if (preg_match('/\w+\.\w+$/', $attributes['image_i'], $m)) {
				if ($m[0] != $user->get('image'))
					$user->set('image', $m[0]);
			}
		}
		$user->save();

	}
	
	public static function mobile_user_profile_update(\ApiParam $params){
		self::mobile_ad_counter($params);
		if(!$params->userId) return null;
		self::updateUserProfile($params->userId,(array)$params);
		$user = self::readUserProfile($params->userId);
		return self::composeResponse(0, '用户资料更新成功', array('user'=>$user));		
	}
	
	public static function mobile_user_profile(\ApiParam $params){
		self::mobile_ad_counter($params);
		if(!$params->userId) return null;
		return self::readUserProfile($params->userId);
	}
	
	public static function sendCode(\ApiParam $params, $type = "resetPassword") {
		
		if (!\Validate::isMobile($params->mobile)) throw new \Exception('手机号码不正确',1001);
		$user = \User::loadByMobile($params->mobile);
		if(!$user) throw new \Exception('手机号码未注册,请先注册新用户',1002);
		if (\Cache::getCountNumber("SendCode_{$params->mobile}", 60) > 1) {
			\InstantCounter::count("SendCode_Retry");
			throw new \Exception('请您1分钟后再试.',1003);
		}
		$code = \Authorization::mobilePassword($params->mobile);
		$content = "";
		if($params->api_key == 'api_mobile_android'){
			$content = "您的手机验证码为{$code}。本条免费，有效期一天";
		}else{
			$content = "您的手机验证码为{$code}，请在手机客户端上输入后继续下一步操作。本条免费，有效期一天";
		}
		\Sms::send($params->mobile, $content, '手机客户端_获取验证码-'.$type);
		return TRUE;//成功发送
	}
	
	public static function mobile_sendsmscode(\ApiParam $params){
		self::mobile_ad_counter($params);
		if(self::sendCode($params)){
		return self::composeResponse(0, '验证码已经发送！');		
		}//否则异常统一处理
	}
	
	private static function resetPassword($mobile,$code,$password,$curPassword=null){
		$mobile_code = \Authorization::mobilePassword($mobile);
		$u = \User::loadByMobile($mobile);
		if(!$u) throw new \Exception('该手机未注册',1005);
		if($u->isFrozen()) throw new \Exception('账户被冻结，无法重置密码，请联系客服。',1007);
		if ($code != $mobile_code 
				&& (!$curPassword || ($curPassword && $u->password != $curPassword))) {
			throw new \Exception('输入的验证码不正确',1004);
		}

		try{
			$u->load($u->id);
			$u->password = md5($password);
			$u->save();
		}catch(\Exception $e){
			throw new \Exception('密码重置失败',1006);
		}

		$c = new \Cache("SmsDelay");
		$mt = $c->get($mobile);
		if ($mt !== false)	$c->delete($mobile);
		return TRUE;
	}
	
	public static function mobile_resetpassword(\ApiParam $params){
		self::mobile_ad_counter($params);
		if(self::resetPassword($params->mobile, $params->code, $params->password, $params->curpassword)){
			return self::composeResponse(0, '密码重置成功');	
		}
	}
	
	//调整为友盟通知更新，同时ota方法已不存在
	//已被 checkVersion 取代，只为兼容旧版本保留，后续清理 zengming
	public static function mobile_checkupdate(\ApiParam $params){
		self::mobile_ad_counter($params);
		if(!$params->version || $params->version != 'v3.0'){
			$result = array("update" => true, "message" =>  "百姓网出新版啦，抢先下载吧！", 'link' => 'http://m.baixing.com/ota', "mandatory" => false);		
		}else{
			$result = array("update" => false, "message" =>  "您的版本已经最新！");	
		}
		return $result;
	}
	
	
	private static function feedbackIsSpam($feedback)
	{
		$content = html_entity_decode( html_entity_decode($feedback));
		if (strpos($content, '<a href=') !== FALSE && strpos($content, '</a>') != FALSE)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	public static function mobile_feedback(\ApiParam $params){
		if($params->adId != null){////caused by android 2.3 2.5 feedback is report, report is feedback
			return self::mobile_report($params);
		}
		self::mobile_ad_counter($params);
		if (!$params->feedback) return self::composeResponse(1, '反馈信息不能为空!');
		if (self::feedbackIsSpam($params->feedback))
		{
			return self::composeResponse(2, '反馈失败');
		}
		$content = ($params->mobile ? $params->mobile : "匿名用户") . "反馈说： " . $params->feedback;
		\Logger::error('百姓网 Mobile App 用户反馈', $content, 'mobile@baixing.com');
		return self::composeResponse(0, '反馈收到，谢谢！');	
	}
	
	/*
	 * for push/notification
	 */
	public static function mobile_tokenupdate(\ApiParam $params){
		self::mobile_ad_counter($params);
		if(self::update_xmpp_push_token($params)){
			return self::composeResponse(0, 'token 更新成功');
		}else{
			return self::composeResponse(1, 'token 更新失败');	
		}
	}


	private static function mylog($message){
		$filename = '/tmp/api_mobile_debug.log';
		$fh = fopen($filename, "a");
		fwrite($fh, $message);
		fclose($fh);
	}
	
	private static function mobile_ad_counter(\ApiParam $params){//2.6之后不再搭载每个网络请求
		if(!$params->adIds) return;
		try {
			Ad::ad_counter($params);
			$ids = array_filter(explode(',', $params->adIds));
			foreach($ids as $adId){
				self::log_ad_counter($params,'succ',$adId);
			}
			$params->adIds = null;//avoid duplicated logging
		} catch(\Exception $e){
			//keep silience, don't throw in case impact following business transaction
		}
	}
	
	public static function mobile_stats(\ApiParam $params){
		self::mobile_ad_counter($params);
		self::prepare_log_stats($params, 'succ');
		//存储各字段到 CAOFEI 服务器
		return self::composeResponse(0, '成功收到数据');
	}

	private static function handleTrackData(\ApiParam $params) {
		$data = json_decode($params->json, true); //$data is an array
		if (!is_array($data)) {
			//\Email::sendMail("zhongjiawu@baixing.com", "json format wrong!", $params->json);
			return self::composeResponse(0, '成功收到数据');
		}
		//组建一个公共属性的array
		$common = array();
		if ($params->api_key)
			$common["api_key"] = $params->api_key;
		if ($params->api)
			$common["api"] = $params->api;
		if ($params->channel)
			$common["channel"] = $params->channel;
		if ($params->udid)
			$common["udid"] = $params->udid;
		if ($params->uid)
			$common["uid"] = $params->uid;
		if ($params->version)
			$common["version"] = $params->version;
		if ($params->appVersion) //for ios3.1 bug, rmove when ios 3.2 release
			$common['version'] = $params->appVersion;
		if ($params->appId) //for chencang
			$common['appId'] = $params->appId;
		
		$adIds = array();
		$paramArray = array();
		foreach ($data as $item) {

			if ($item['tracktype'] == 'pageview' || $item['tracktype'] == 'event') {
				self::$customLog = array_unique(array_merge(self::$customLog, array_keys($item)));
				$item = array_merge((array)$item, $common); //每个item都是一个记录信息的array
				$param = \Api::paramFactory($item);
				$paramArray[] = $param;
				if ($param->tracktype == "pageview" && isset($param->pageURL) && $param->pageURL == "/viewAd" && isset($param->adId)) {
					$adIds[] = $param->adId;

					$realApi = $params->api;
					$param->api = 'mobile.ad_counter'; //fake API name
					\Api_Util::eventLog($param, "succ", array('adId')); //$params,'succ',$adId
					$param->api = $realApi;
				}
			}
		}
		$params->paramArray = $paramArray;
		$params->is_array = "1";
		if (count($adIds) > 0) {//一组记录组成的一条记录，其中有adIds参数
			$infoArray = array_merge($common, array("tracktype" => "pageview", "pageURL" => "/viewAd", "adIds" => $adIds));
			$infoParam = \Api::paramFactory($infoArray);
			\Api\Ad::ad_counter($infoParam);
		}
	}
	
	public static function mobile_trackdata(\ApiParam $params) {
		//mobile_trackdata
		self::prepare_log_trackdata($params, 'succ');
		self::handleTrackData($params);
		return self::composeResponse(0, '成功收到数据');
	}

	private static function parseCityCategoryFromQuery($query){
		$result = array('city' => '','category' => '');
		$matches = array();
		if(preg_match("/cityEnglishName:(.*?)(\s|$)/",$query,$matches)){
			if(count($matches) >1){
				$result['city'] = $matches[1];
			}
		}
		if(preg_match("/categoryEnglishName:(.*?)(\s|$)/",$query,$matches))
		{
			if(count($matches) >1){
				$result['category'] = $matches[1];
			}
		}
		return $result;
	}
	
	private static function prepare_log_ad_list(\ApiParam $params,$status,$msg = null) {
		$cc = self::parseCityCategoryFromQuery($params->query);
		$params->_city = $cc['city'];
		$params->_category = $cc['category'];
		self::$customLog = array('_city', '_category');//logged in ApiController
	}
	/*
		[需求]
		call 打电话按钮点击数 
		sms 短信按钮点击数
		weibo 微博按钮点击数
		weixin 微信按钮点击数
		vad 查看viewad次数
		addcontact 添加到通讯录

		hots 热点点击数
		homesearch 首页搜索数量
		listingfilter listing筛选数量
		listingbydate listingbydist 按时间排序、按距离排序数量
	*/
	private static function prepare_log_stats(\ApiParam $params,$status,$msg = null) {
		self::$customLog = array('call', 'sms', 'weibo', 'weixin','sixin','vad','addcontact',
			'hots','homesearch','listingfilter','listingbydate','listingbydist', 'getNotification', 'clickNotification');//logged in ApiController
	}
	
	private static function prepare_log_trackdata(\ApiParam $params, $status, $msg = null) {
		self::$customLog = array();
	}
	
	private static function log_ad_counter(\ApiParam $params,$status, $adId,$msg = null) {
		if(!$adId) return;
		$realApi = $params->api;
		$params->api = 'mobile.ad_counter';//fake API name
		$params->adid = $adId;
		\Api_Util::eventLog($params, $status, array('adid'), $msg);
		$params->api = $realApi;
	}
	
	private static function log_ad_add(\ApiParam $params,$status, $adId,$msg = null) {
		if(!$adId) return;
		$realApi = $params->api;
		$params->api = 'mobile.ad_add_success';//fake API name
		$params->adid = $adId;
		\Api_Util::eventLog($params, $status, array('adid'), $msg);
		$params->api = $realApi;
	}
		
	public static function mobile_debug(\ApiParam $params) {
		//mobile_trackdata
		if (!$params->type) {
			return self::formatResult(403, 'No Found "type"');
		}
		$result = null;
		switch ($params->type) {
			case 'push':
				$result = self::mobile_debug_push($params);
				break;
			default:
				$result = self::formatResult(403, 'Error "type"');
				break;
		}
		return $result;
	}
	
	

	//composeResponse 封装的格式不好用，换这个
	private static function formatResult($code = 0, $msg = 'OK', $arr = null) {
		$result = array('code' => $code, 'msg' => $msg);
		if (is_array($arr)) {
			$result = array_merge($result, $arr);
		}
		return $result;
	}

	private static function mobile_debug_push(\ApiParam $params) {
		$udid = $params->udid;
		$apiKey = $params->api_key;
		$deviceToken = $params->deviceToken;

		$action = $params->action ?: "info";
		$title = $params->title 
			?: "<" . $action . "> Pust Test Title 0 1 2 3 4 5 6 7 8 9 10." 
				. " 0 1 2 3 4 5 6 7 8 9 10. 0 1 2 3 4 5 6 7 8 9 10.";
		$title = date('[m-d h:m:s]') . $title;
		$data = $params->data ?: "";
		$data = htmlspecialchars_decode($data);
		$jsonData = json_decode($data, true);
		if (!empty($data) && !is_array($jsonData) ) {
			return self::formatResult(403, '"data" 格式错误');
		}

		if (empty($udid) || empty($apiKey)) {
			return self::formatResult(403, '缺少参数 "udid" or "api_key"');
		}
		if ($apiKey != "api_mobile_android" && empty($deviceToken)) {
			return self::formatResult(403, '缺少参数 "deviceToken"');
		}

		$notificationToken = new \NotificationToken();
		$pushData = null;
		$queryArray = new \AndQuery(
			new \Query('appType', $apiKey), 
			new \Query('deviceUniqueIdentifier', $udid));
		if ($apiKey != "api_mobile_android") { // 非 Android 需要 deviceToken
			$queryArray = new \AndQuery( $queryArray, 
				new \Query('deviceToken', $deviceToken));
		}

		$targets = $notificationToken->find($queryArray);
		if (empty($targets)) {
			return self::formatResult(403, 'Push表中无结果');
		} else if (count($targets) > 1) {
			return self::formatResult(403, 'Push表异常，查出多个结果:' . count($targets));
		}

		if (\Cache::getCountNumber('PushTestCount', 24*3600) > 100) {
			return self::formatResult(403, '测试超限');
		}

		\PushNotification::sendByTokens($title, $targets, 
			$action, $title, $data);

		return self::formatResult();
	}
	
	public static function mobile_verifyMobile(\ApiParam $params){
		if(!$params->mobile) throw new \Exception('无效的手机号', 503);
		$mobileUser = \User::loadByMobile($params->mobile);
		if($mobileUser && $params->verifyCode){
			$code = \Authorization::mobilePassword($params->mobile);
			if($params->verifyCode == $code){
				$mobileUser->linkToMobile($params->mobile, true);
			}else{
				throw new \Exception('输入的验证码不正确',1004);
			}
		}
		return ($mobileUser && $mobileUser->isMobileVerified()) ? self::composeResponse(0, "verify succeeds") : self::composeResponse(1, "verify fails");
	}
	
	public static function mobile_checkAccountStatus(\ApiParam $params){
		if(!$params->mobile && !$params->nickname) throw new \Exception('无效的帐号', 503);
		$mobileUser = null;
		if($params->mobile){
			$mobileUser = \User::loadByMobile($params->mobile);
			if(!$mobileUser && $params->nickname){
				$mobileUser = \User::loadByNickname($params->nickname);
			}
		}
		if(!$mobileUser){
			return self::composeResponse(1, "account not registered");
		}
		if($mobileUser->isMobileVerified()){
			return self::composeResponse(2, "verified");
		}else{
			return self::composeResponse(3, "unverified");
		}
	}
	
	private static function encrypt($password, $apiKey){
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$key = self::getApiSecret($apiKey);
		if(strlen($key) > 16){
			$key = substr($key, 0, 16);
		}
		$crypttext = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $password, MCRYPT_MODE_ECB, $iv));
		return $crypttext;	
	}
	
	public static function mobile_getUser(\ApiParam $params){
		if(!$params->mobile) throw new \Exception('无效的手机号', 503);
		$user = \User::loadByMobile($params->mobile);
		if(!$user){
			return self::composeResponse(1, "user not registered");
		}
		$encrypted = self::encrypt($user->password, $params->api_key);

		return self::composeResponse(0, "获取信息成功", array('id' => $user->id,
												'mobile' => $user->mobile,
												'nickname' => $user->nickname,
												'createdTime' => $user->createdTime,
												'password' => $encrypted));
	}
	
	public static function mobile_getAroundAds(\ApiParam $params){	
		$cityEnglishName = '';
		$queryHasCityEnglishName = FALSE;
		if($params->query){
			if(preg_match('/\w*cityEnglishName:\w*/', $params->query, $match)){
				$queryHasCityEnglishName = TRUE;
				if(preg_match('/(?<=cityEnglishName:)\w*/', $match[0], $matchCity)){
					$cityEnglishName = $matchCity[0];				
				}
			}
		}
		if($cityEnglishName == ''){
			$cityEnglishName = $params->cityEnglishName;
			if($cityEnglishName == ''){
				throw new \Exception("无效城市名", 888);
			}
		}
		$originCity = \City::loadByName($cityEnglishName);
		if(!$originCity){
			throw new \Exception("无效城市名", 888);
		}
		$cities = null;		
		$newCityQuery = '';
		if($originCity){
			$aroundCities = $originCity->getAroundCities();
			$cities = array();
			foreach($aroundCities as $objId => $city){
				$cities[$objId]['name'] = $city->get('name');
				if($newCityQuery == ''){
					$newCityQuery = 'cityEnglishName:'.$city->get('englishName');
				}else{
					$newCityQuery = $newCityQuery.' OR cityEnglishName:'.$city->get('englishName');
				}
				
			}
			$newCityQuery = '(' . $newCityQuery . ')';
		}
		
		$copy = $params->getArrayCopy();
		unset($copy['cityEnglishName']);
		if($queryHasCityEnglishName){
			$copy['query'] = preg_replace('/cityEnglishName:\w*/', $newCityQuery, $copy['query'], 1);
		}else{
			$copy['query'] = $copy['query'].' '.$newCityQuery;
		}
//		var_dump($copy);
		$newParams = \Api::paramFactory($copy);
//		var_dump($newParams);
		return self::mobile_ad_list($newParams);
	}
	
	public static function mobile_get_quota_info(\ApiParam $params){	
		$user = new \User();
		$user->load($params->userId);
		$city = \City::loadByName($params->cityEnglishName);
		$category = \Category::loadByName($params->categoryEnglishName);
		if(!$user || !$city || !$category){
			throw new \Exception('参数错误', 777);
		}
		$quotaInfo = \Fabu_Logic::quotaInfo($user, $city, $category);
		$quotaInfo['message'] = strip_tags($quotaInfo['message']);
		$result = array('quota' => $quotaInfo);
		return self::composeResponse(0, 'succeed', $result);
	}
	
	public static function mobile_get_favourites(\ApiParam $params){
		$user = new \User();
		$user->load($params->userId);
		$follower = \Follower::create($user);
		if($follower){
			$favs = $follower->favovites();
			$originFavs = $favs;
			if($favs && is_array($favs)){
				$query = '(';
				foreach($favs as $fav){
					if($fav->identity){
						if($query != '('){
							$query .= ' OR ';
						}
						$query .= 'id:' . $fav->identity;
					}
				}
				if($query != '('){
					$query .= ')';
					$ary = $params->getArrayCopy();
					$ary['query'] = $query;		
					$newParams = \Api::paramFactory($ary);
					$response = self::mobile_ad_list($newParams);
					$existingAds = $response['data'];
					if($existingAds){
						foreach($existingAds as $ad){
							foreach($favs as $key => $fav){
								if($ad['id'] == $fav->identity){
									unset($favs[$key]);
									break;
								}
							}
						}
					}
					if(sizeof($favs) > 0){
						foreach($favs as $fav){
							array_push($existingAds, array('id' => $fav->identity,
										'title' => $fav->attris['title'],
										'status' => '3'));
							++ $response['num'];
						}
					}
					
					$adsMap = array();
					foreach($existingAds as $ad){
						$adsMap[$ad['id']] = $ad;
					}
//					var_dump('ads map');
//					var_dump($adsMap);
					
					$sortAds = array();
					foreach($originFavs as $fav){
						array_push($sortAds, $adsMap[$fav->identity]);
					}
//					var_dump('sorted ads');
//					var_dump($sortAds);
					
					$response['data'] = $sortAds;
					
					return $response;
				}
			}else{
				return self::composeResponse(0, '没有已收藏信息', array('count' => 0, 'data' => array(), 'num' => 0));
			}
		}
		return self::composeResponse(1, 'fail');		
	}
	
	public static function mobile_add_favourites(\ApiParam $params){	
		$user = User::user_show($params);
		
		$ids = explode(',', $params->adIds);
		if(count($ids) < 1){
			throw new \Exception('参数错误', 333);
		}
		
		$exception = false;
		$follow = null;
		foreach($ids as $id){
			$loader = new \Follow\Favorite($user);
			$oldFollow = $loader->exists($id);
			if($oldFollow && !empty($oldFollow)){
				$exception = true;
				continue;
			}
			$newFollow = $loader->create($id);
			if(!$follow){
				$follow = $newFollow;
			}
		}
		$code = 0;
		$message = '收藏成功';
		if(count($ids) > 1){
			$code = $follow ? 0 : 1;
			if(!$follow){
				$message ='重复添加';
			}
		}else{
			if($exception){
				throw new \Exception('重复添加');
			}
		}
		return self::composeResponse($code, $message);
	}
	
	public static function mobile_remove_favourites(\ApiParam $params){	
		$user = User::user_show($params);

		$class = "Follow\\Favorite";
		$loader = new $class($user);
		$follow = $loader->exists($params->adId);
		if(!$follow || empty($follow)){
			throw new \Exception('不是已收藏信息', 222);
		}
		
		$loader->destory($params->adId);
		$success = !$loader->exists($params->adId);
		return self::composeResponse($success ? 0 : 1, $success ? '取消收藏成功' : '取消收藏失败');		
	}

	public static function mobile_ad_reported(\ApiParam $params){
		$logic = new \Logic\Feedback\Jubao_Logic();
		$can = $logic->canJubao($params->adId, $params->mobile);
		return self::composeResponse(0, $can ? "未举报" : "已举报", array('reported' => !$can));
	}
}
