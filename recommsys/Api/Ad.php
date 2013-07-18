<?php
//zhaojun@baixing.com
namespace Api;
class Ad extends \Api implements \ApiLimitation, \ApiList {

	public static function apiList() {
		$apiList = array();
		//write
		$apiList['ad.delete'] = array(
			'require' => array('adId', 'mobile', 'userToken'),
			'optional' => array('nickname'),
			'return' => \ApiList::API_RETURN_BOOL,
			'type' => \ApiList::API_TYPE_WRITE,
		);
		$apiList['ad.undelete'] = array(
			'require' => array('adId', 'mobile', 'userToken'),
			'optional' => array('nickname'),
			'return' => \ApiList::API_RETURN_BOOL,
			'type' => \ApiList::API_TYPE_WRITE,
		);
		$apiList['ad.update'] = array(
			'require' => array('title', 'description', 'contact', 'cityEnglishName', 'categoryEnglishName', 'mobile', 'userToken'),
			'optional' => array('adId', 'lat', 'lng', 'api_xxx', 'images'),
			'return' => \ApiList::API_RETURN_STRING,
			'type' => \ApiList::API_TYPE_WRITE,
		);
		$apiList['ad.refresh'] = array(
			'require' => array('adId', 'mobile', 'userToken'),
			'optional' => array('nickname'),
			'return' => \ApiList::API_RETURN_STRING,
			'type' => \ApiList::API_TYPE_WRITE,
		);
		$apiList['ad.pintotop'] = array(
			'require' => array('adId', 'mobile', 'userToken'),
			'return' => \ApiList::API_RETURN_ARRAY,
			'type' => \ApiList::API_TYPE_WRITE,
		);
		$apiList['ad.report'] = array(
			'require' => array('adId', 'mobile','description' , 'userToken'),
			'optional' => array('userId'),
			'return' => \ApiList::API_RETURN_ARRAY,
			'type' => \ApiList::API_TYPE_WRITE,
		);
		$apiList['ad.appeal'] = array(
			'require' => array('adId', 'mobile','description' , 'userToken'),
			'optional' => array('userId'),
			'return' => \ApiList::API_RETURN_ARRAY,
			'type' => \ApiList::API_TYPE_WRITE,
		);
		$apiList['ad.counter'] = array(
			'require' => array('adIds'),
			'return' => \ApiList::API_RETURN_BOOL,
			'type' => \ApiList::API_TYPE_WRITE,
		);
		
		//read
		$apiList['ad.load'] = array(
			'require' => array('adId'),
			'optional' => array('nickname'),
			'return' => \ApiList::API_RETURN_ARRAY,
			'type' => \ApiList::API_TYPE_READ,
		);
		$apiList['ad.list'] = array(
			'require' => array(),
			'optional' => array('query', 'rows', 'start', 'rt', 'fields','activeOnly','inactiveOnly'),
			'return' => \ApiList::API_RETURN_ARRAY,
			'type' => \ApiList::API_TYPE_READ,
		);
		$apiList['ad.nearby'] = array(
			'require' => array('lat', 'lng'),
			'optional' => array('query', 'rows', 'start', 'rt', 'd', 'fields'),
			'return' => \ApiList::API_RETURN_ARRAY,
			'type' => \ApiList::API_TYPE_READ,
		);
		$apiList['ad.microcontrol'] = array(
			'require' => array('adId', 'mobile', 'userToken'),
			'optional' => array('nickname'),
			'return' => \ApiList::API_RETURN_ARRAY,
			'type' => \ApiList::API_TYPE_READ,
		);
		$apiList['ad.recommend'] = array(
			'require' => array('cityEnglishName'),
			'optional' => array('lat', 'lng'),
			'return' => \ApiList::API_RETURN_ARRAY,
			'type' => \ApiList::API_TYPE_READ,
		);
		return $apiList;
	}

	public static function limitation(\ApiParam $params) {
		return;
	}

	public static function ad_undelete(\ApiParam $params) {
		$user = User::user_show($params);
		$ad = self::loadAndCheck($params->adId, $user, false, $params->udid);
		if($ad->status != \Ad::STATUS_DELETED_BY_SELF){
			return false;
		}
		$bakStatus = $ad->status;
		$ad->status = $ad->get('bakStatus');//恢复之前状态
		$ad->set('bakStatus', $bakStatus); //保存删除前的状态以备用户误删恢复时用
		$ad->save();
		\AdLogger::log($user, $ad, $bakStatus, $ad->status, 'via API: ' . $params->api_key , \AdLogger::TYPE_RECOVERY);
		return true;
	}
	
	public static function ad_delete(\ApiParam $params) {
		$user = User::user_show($params);
		$ad = self::loadAndCheck($params->adId, $user, false,$params->udid);
		$bakStatus = $ad->status;
		$ad->status = \Ad::STATUS_DELETED_BY_SELF;
		$ad->set('userDeletedTime', time());
		$ad->set('bakStatus', $bakStatus); //保存删除前的状态以备用户误删恢复时用
		
		if($bakStatus == \Ad::STATUS_DELETED_BY_SELF){
			//final delete
			$ad->set('final_delete', 1);
		}
		$ad->save();
		\AdLogger::log($user, $ad, $ad->get('bakStatus'), \Ad::STATUS_DELETED_BY_SELF, 'via API: ' . $params->api_key, \AdLogger::TYPE_DELETE_BY_SELF);
		return true;
	}

	public static function ad_microcontrol(\ApiParam $params) {
		return array();
	}

	public static function ad_counter(\ApiParam $params) {
		$ids = array_filter(explode(',', $params->adIds));
		if (count($ids) > 100) return 'limit 100';
		$main	= curl_multi_init();
		$handles = array();
		foreach ($ids as $key => $id) {
			$url = "http://counter.baixing.net/c.php?id={$id}";
			$handles[$key] = curl_init($url);
			curl_setopt($handles[$key], CURLOPT_URL, $url);
			curl_setopt($handles[$key], CURLOPT_RETURNTRANSFER, 1);
			curl_multi_add_handle($main, $handles[$key]);
			\Logger::eventLog('adcounter', (array)$params);
		}

		$running = 0;
		$i = 0;
		do {
			curl_multi_exec($main, $running);
			$i++;
			usleep(1000);
		} while($running > 0 && $i < 1000);

		foreach ($ids as $key => $id) {
			//$results[] = curl_multi_getcontent($handles[$key]);
			curl_multi_remove_handle($main, $handles[$key]);
		}
		curl_multi_close($main);
		return 'ok';
	}

	public static function ad_update(\ApiParam $params) {
		
		
		$user = User::user_show($params);
		$category = $params->categoryEnglishName ? \Category::loadByName($params->categoryEnglishName) : null;
		$city = $params->cityEnglishName ? \City::loadByName ($params->cityEnglishName) : null;
		
		$ad = self::buildAd($params, $user);
		
		
		//@坑 太坑爹了吧，方法叫ad_update,里面却还区分insert和update，这不是挂羊头卖狗肉么。 By yubing
		$isBianji = isset($ad->id);
		
		//Api Attributes
		$ad->set('postMethod', $params->api_key);

		if (!$isBianji) $ad->set('uuid', $params->uuid);

		$ad->set('udid', $params->udid);

		if(in_array($params->api_key,array('api_mobile_android','baixing_ios', 'api_iosbaixing'))){
			$ad->set('uuid',$params->udid);
		}
		// 过滤非法的位置信息
		if (is_numeric($params->lat) && is_numeric($params->lng) && 
				($params->lat > -180 && $params->lat < 180) &&
				($params->lng > -180 && $params->lng < 180) )
		{
			$ad->set('lat', $params->lat);
			$ad->set('lng', $params->lng);
		}
		$i = 0;
		foreach ($params as $key => $value) {
			if (substr($key, 0, 4) == 'api_') {
				$ad->set($key, $value);
				$i++;
			}
			if ($i >= 50) break;
		}

		//Save Ad
		$ad = \Fabu_Logic::saveAd($ad, $user);
		
		new \Action\PostAd($ad);

		$ruleType = $isBianji ? \RuleSet::TYPE_API_MODIFY : \RuleSet::TYPE_API_ALL;

		//bobo加的，确保WAP站上来的信息都走所有的Rule
		if($ad->get('postMethod') == 'api_wap')
			$ruleType = $isBianji ? \RuleSet::TYPE_WEB_MODIFY : \RuleSet::TYPE_WEB_ALL;

		try {
			$ad->checkMe($ruleType);
		} catch (\Exception $e) {
			return array('adId' => $ad->id, 'code' => $e->getCode(), 'msg' => $e->getMessage());
		}

		if (!$params->mutiResponse) {
			return $ad->id;
		} else {
			if ($ad->status != \Ad::STATUS_ACTIVE) {
				return array('adId' => $ad->id, 'code' => '505', 'msg' => $ad->get('lastOperation'),
					'tips' => self::generateTips($ad->get('lastOperation'), $user, $category, $city));
			} else {
				return array('adId' => $ad->id, 'code' => 0, 'msg' => '发布成功');
			}
		}
	}
	
	
	private static function generateTips($lastOperation,$user,$category,$city){
		if($lastOperation == '类目发布超限'){
			$quota = new \PostQuota($user->id, $category->englishName, $city->englishName);
			return '发布失败，该分类每月可免费发布' . $quota->total() . '条信息，您本月的额度已经用完。如需多发请访问 http://www.baixing.com 使用付费产品。';
		}else if($lastOperation == '城市发布超限'){
			return '发布失败，百姓网只允许您将信息发布在一个城市。如需多发请访问 http://www.baixing.com 使用付费产品。';
		}else{
			return strip_tags(\Bangui::description($lastOperation,false));
		}
	}

	public static function ad_refresh(\ApiParam $params) {
		$user = User::user_show($params);
		$ad = self::loadAndCheck($params->adId,$user,false,$params->udid);
		if ($user->id != $ad->userId) {
			return array('msg' => '您没有权限进行此操作');
		}

		//免费刷新
		if ( \Business::ableToFreeRefresh($ad) ) {
			\Ad::refresh($ad->id);
			return array('msg' => '免费刷新成功');
		}

		if (\Business::maintainingMessage() && \Business::stopWrite()) {
			throw new \Exception(\Business::maintainingMessage());
		}

		//余额刷新
		if ($user->money() >= f2y(\Business::refreshPrice($ad->cityEnglishName(), $ad->categoryEnglishName, $ad->userId))) {
			$oid	= \Business::createOrder($ad->id, \Order::TYPE_REFRESH);
			\Business::pay($oid);
			return array('msg' => '余额刷新成功');
		}
		return array('msg' => '刷新失败', 'x' => "{$ad->posterType()}", 'time' => "{$ad->createdTime}");
	}

	public static function ad_nearby(\ApiParam $params) {
		if (!$params->lat || !$params->lng) throw new \Exception('need lat and lng', 501);
		list($query, $category, $city, $wanted, $options, $realTime) = self::searchOption($params);
		$searcher = new \DistanceSearcher(new \LatLng($params->lat, $params->lng), $query, $city, $category, $wanted, $options, $realTime);
		$ads = $searcher->ads();
		if($params->rows){
			$ads = array_slice($ads, 0,$params->rows);
		}
		
		$allAds = self::ads($ads, $params->fields, $params->valueFlag);
		
		usort($allAds, function($ad1, $ad2){
			$t1 = $ad1['createdTime'];
			$t2 = $ad2['createdTime'];
			if ($t1 < $t2)
				return 1;
			else if ($t1 > $t2)
				return -1;
			else
				return 0;
		});
		
		return array('count' => $searcher->totalCount(), 'data' => $allAds);
	}

	public static function ad_recommend(\ApiParam $params) {
		list($query, $category, $city, $wanted, $options, $realTime) = self::searchOption($params, 'insertedTime desc');

		if(!$city) throw new \Exception('need cityEnglishName', 501);

		$query = new \AndQuery($query, new \Query('status', \Ad::STATUS_ACTIVE));
		$query = new \AndQuery($query, new \Query('cityEnglishName', $city->englishName));

		if ($category)
			$query = new \AndQuery($query, new \Query('categoryEnglishName', $category->englishName));
		if (isset($wanted))
			$query = new \AndQuery($query, new \Query('wanted', $wanted));

		if($params->lat && $params->lng) {
			if($params->algorithm == null || $params->algorithm == '1')
				//圆形查询
				$query = new \AndQuery($query, new \LatlngQuery(new \LatLng($params->lat, $params->lng), 3.0));
			else if($params->algorithm == '2') {
				//正方形查询
				$deltaLat = 3.0 * 1/111.413; //3KM的纬度差
				$latRangeQuery = new \RangeQuery('lat', $params->lat - $deltaLat, $params->lat + $deltaLat);
				$deltaLng = 3.0 * 1/(111.413 * cos(deg2rad($params->lat)) - 0.094 * cos(deg2rad($params->lat)));//3KM的经度差
				$lngRangeQuery = new \RangeQuery('lng', $params->lng - $deltaLng, $params->lng + $deltaLng);
				$query = new \AndQuery($query, $latRangeQuery, $lngRangeQuery);
			}
		}

		$searcher = new \ActiveSearcher($query, $options);
		$allAds = self::ads($searcher->ads(), $params->fields, $params->valueFlag);

		return array('count' => $searcher->totalCount(), 'data' => $allAds);

	}

	public static function ad_load(\ApiParam $params) {//load robust
		try {
			$ad = self::loadAndCheck($params->adId, null, false);
		} catch (\Exception $e) {
			return array('code' => $e->getCode(), 'data' => null);
		}
		return array(
			'code' => 0,
			'data' => self::ads(array($ad), null, null, false),
			'isArchive' => false //兼容老接口，避免外部程序出错。
		);
	}
		
	private static function loadAd($ad_id){//load from realtime db
		try{
			$ad = new \Ad();
			$ad->id = $ad_id;
			return $ad->load();
		}catch(\Exception $e){
			return null;
		}
	}
	
	private static function exist($ad_id, $adsSearched){
		foreach($adsSearched as $ad){
			if($ad->id == $ad_id){
				return true;
			}
		}
		return false;
	}

	private static function searchAds(\ApiParam $params) {
		list($query, $category, $city, $wanted, $options, $realTime) = self::searchOption($params, 'createdTime desc');

		$activeOnly = ($params->activeOnly==1);
		$inactiveOnly = ($params->inactiveOnly==1);

		$totalCount = 0;
	
		if($inactiveOnly){
			$searcher = new \InactiveSearcher($query,$options);
		}else{
			if ($activeOnly)
				$query = new \AndQuery($query, new \Query('status', \Ad::STATUS_ACTIVE));
			if ($category)
				$query = new \AndQuery($query, new \Query('categoryEnglishName', $category->englishName));
			if ($city)
				$query = new \AndQuery($query, new \Query('cityEnglishName', $city->englishName));
			if (isset($wanted))
				$query = new \AndQuery($query, new \Query('wanted', $wanted));
			
			$searcher = new \ActiveSearcher($query, $options);
			
			if (!(isset($options['start']) && $options['start'] > 0)) // 只在第一页搜rt库
			{
				if ($params->isUserAd)
				{
					$searcher->prepend(new \RtSearcher($query, $options));				
				}else
				{
					// 修改过的很老的数据也会进rt库，只搜最近11分钟内创建的AD  @zhongjiawu 2012-12-13
					$rtQuery = new \AndQuery(
						$query,
						new \DateFrangeQuery('createdTime', strtotime(date('Y-m-d H:i:00', time() - 660)))
					);
					$searcher->prepend(new \RtSearcher($rtQuery, $options));											
				}
				
			}

			if (!$activeOnly)	$searcher->prepend(new \InactiveSearcher($query, $options));
		}
		$totalCount += $searcher->totalCount();
		
		return array($totalCount, $searcher->ads());
	}

	//<status> 0 
	//0. 正常显示 1. 未通过审核 2. 已删除 3. 正常显示 ＋ 未审核通过
	public static function ad_user_list(\ApiParam $params){
		$ACTIVE = \Ad::STATUS_ACTIVE;//正常显示
		$DELETED_BY_SELF = \Ad::STATUS_DELETED_BY_SELF;//
		$SUSPENDED = \Ad::STATUS_SUSPENDED;//未通过审核
		$PENDING = \Ad::STATUS_PENDING;//未通过审核
		
		$userId1 = $params->userId;
//		var_dump($params);
		$userId2 = self::loadUserByUdid($params->udid);
		if($params->status == 0){
			$query_status = "(status:{$ACTIVE})";
		}else if($params->status == 1){
			$query_status = "(status:{$SUSPENDED} OR status:{$PENDING})";
		}else if($params->status == 2){
			$query_status = "(status:{$DELETED_BY_SELF})";
		}else if($params->status == 3){
			$query_status = "(status:{$SUSPENDED} OR status:{$PENDING} OR status:{$ACTIVE})";
		}else{
			$query_status = '*:*';
		}
		$query_userid1 = $userId1? "(userId:{$userId1})" : '()';
		$query_userid2 = $userId2? "(userId:{$userId2})" : '()';
		if($userId1 && $userId2){
			$query = $query_status . ' AND ' . '(' . $query_userid1 . ' OR ' . $query_userid2 . ')'; 
		}else{
			if($userId1){
				$query = $query_status . ' AND ' . $query_userid1;
			}else{
				$query = $query_status . ' AND ' . $query_userid2;
			}
		}
		$params->query = $query;

//		var_dump($params->query);
		//echo '[query]' . $params->query;
		
		
		$params->isUserAd = TRUE;
		list($resultCount, $adsSearched) = self::searchAds($params);
		$ads = self::loadRealtimeAds($params->newAdIds,$adsSearched);
		$resultCount += count($ads);
		$ads = array_merge($ads,$adsSearched);

		if($params->rows){		
		 	$ads = array_slice($ads, 0, $params->rows);
		}

		usort($ads, function($ad1, $ad2){
			$t1 = $ad1->createdTime;
			$t2 = $ad2->createdTime;
			if ($t1 < $t2)
				return 1;
			else if ($t1 > $t2)
				return -1;
			else
				return 0;
		});

		$allAds = self::ads($ads, $params->fields, $params->valueFlag, $params->activeOnly);
	 	
		
		return array('count' => $resultCount, 'data' => $allAds);
	}
	
	private static function loadUserByUdid($udid){
		$user = null;
		if(\Auth::exist(\Auth::TYPE_CLIENT, $udid)){
			$user = \Auth::login(\Auth::TYPE_CLIENT, $udid, null);
		}
		return $user == null ? null : $user->id;
	}
	
	//load ads (return ad id) from DB while not available in Solr (by liuweili@baixing.com)
	private static function loadRealtimeAds($newAdIds,$adsSearched){
		$ads = array();
		$ids = explode(',', $newAdIds);
		foreach($ids as $ad_id){
			if(self::exist($ad_id,$adsSearched)){
				continue;
			}
			$ad = self::loadAd($ad_id);
			if($ad){
				$ads[] = $ad;
			}
		}
		return $ads;
	}
	public static function ad_list(\ApiParam $params) {
		if(!$params->exists('activeOnly')) 
			$params->activeOnly = 1;//没有参数，则默认返回active ad，保证兼容
		list($resultCount, $adsSearched) = self::searchAds($params);
		$ads = self::loadRealtimeAds($params->newAdIds,$adsSearched);
		$resultCount += count($ads);
		$ads = array_merge($ads,$adsSearched);
		if($params->rows){
			$ads = array_slice($ads, 0,$params->rows);
		}
		$allAds = self::ads($ads, $params->fields, $params->valueFlag, $params->activeOnly);
		
		usort($allAds, function($ad1, $ad2){
			$t1 = $ad1['createdTime'];
			$t2 = $ad2['createdTime'];
			if ($t1 < $t2)
				return 1;
			else if ($t1 > $t2)
				return -1;
			else
				return 0;
		});
		
		return array('count' => $resultCount, 'data' => $allAds);
	}
	
	// ad_list里的ads()的耗时太长，150条ad花费大概0.5s.
	// simple_list:只返回id、title等最简单数据，不调用ads.
	public static function ad_simple_list(\ApiParam $params) {
		if(!$params->exists('activeOnly')) 
			$params->activeOnly = 1;//没有参数，则默认返回active ad，保证兼容
		list($resultCount, $adsSearched) = self::searchAds($params);
		$ads = self::loadRealtimeAds($params->newAdIds,$adsSearched);
		$resultCount += count($ads);
		$ads = array_merge($ads,$adsSearched);
		if ($params->rows){
			$ads = array_slice($ads, 0, $params->rows);
		}
		
		$newAds = array();
		foreach ($ads as $ad) {
			$newAd = array();
			$newAd['id'] = $ad->get('id');
			$newAd['title'] = $ad->get('title');
			$newAd['imageFlag'] = $ad->get('imageFlag');
			$newAd['createdTime'] = $ad->get('createdTime');
			$newAds[]= $newAd;
		}
		
		return array('count' => $resultCount, 'data' => $newAds);
	}
	
	public static function ad_report(\ApiParam $params) {
		$mobile  = $params->mobile;
		$description = $params->description;
		$adId = $params->adId;
		try{
			
			$fb = \Feedback::createFeedbackByAd(\Feedback::TYPE_JUBAO,$adId,$mobile,$description);
			if(!$fb) { 
				return array('msg' => '举报失败','adId' => $adId);
			}
		}catch(\Exception $e){
			return array('msg' => '举报失败','adId' => $adId,'error' => $e);
		}		
		return array('feedbackId' => $fb->id);
	}
	
	
	public static function ad_appeal(\ApiParam $params) {
		$mobile  = $params->mobile;
		$description = $params->description;
		$adId = $params->adId;
		try{
			$fb = \Feedback::createFeedbackByAd(\Feedback::TYPE_SHENSU,$adId,$mobile,$description);
			if(!$fb) { 
				return array('msg' => '申诉失败','adId' => $adId);
			}
		}catch(\Exception $e){
			return array('msg' => '申诉失败','adId' => $adId,'error' => $e);
		}		
		return array('feedbackId' => $fb->id);
	}

	/**
	 * @param \ApiParam $params
	 * @param \User $user
	 * @return \Ad
	 * @throws \Exception
	 */
	public static function buildAd(\ApiParam $params, \User $user) {
		if ($params->adId) {
			$ad = self::loadAndCheck($params->adId, $user, false, $params->udid);
			$ad->set('modifyIp', \Ip::remote_addr());
		} else {
			$ad = new \Ad();
			$ad->userIp = \Ip::remote_addr();
			$ad->status = \Ad::STATUS_ACTIVE;
			$ad->userId = $user->id;

			//City 编辑信息不能更换city
			$city = \City::loadByName($params->cityEnglishName);
			if (!isset(\Page::$context['city'])) \Page::$context['city'] = $city;
			if (!$city) throw new \Exception('invalid city', 504);
			$ad->areaCityLevelId = $city->oid;

			$ad->set('totalReport', 0);
			$ad->set('MasterCheckDate', 0);
		}

		//Category
		if (!$params->adId) $ad->categoryEnglishName = $params->categoryEnglishName;
		else $ad->categoryEnglishName = $ad->categoryEnglishName;
		try {
			$category = \Category::loadByName($ad->categoryEnglishName);
		} catch (\Exception $e) {
			throw new \Exception('该类目不存在', '407');
		}
		if ($category->level != \Category::LEVEL_SECOND) {
			throw new \Exception('信息只能发布到二级类目', '406');
		}

		//联系方式
		$params->contact = $params->contact ?: $params->mobile;

		//@todo 如果没有写发布人的，加上发布人属性，保留到2012年9月2日 by zhaojun
		if (!$params->faburen) {
			$ad->setPosterType(null);
			if ($ad->get('faburen')) $params->faburen = $ad->get('faburen');
			else{
				$params->faburen = 'm33660';//dirty fix, but it works(liuweili@baixing.com)
			}
		}

		$metas = $category->metas();
		if ($params->wanted) {
			switch ($category->parentEnglishName) {
				case 'gongzuo':
				case 'jianzhi':
					$metas = \Category::loadByName('qiuzhijianli')->metas();
					break;
				case 'ershou':
					$metas = \Category::loadByName('qiumai')->metas();
					break;
				case 'cheliang':
					$metas = \Category::loadByName('cheliangqiugou')->metas();
					break;
				default :
					$metas = \Category::loadByName('qiufang')->metas();
					break;
			}
		}

		foreach ($metas as $meta) {
			$data = $meta->dataValue($params);
			$dataWithoutUnit = $params->{$meta->name};
			if ((strpos($meta->style, 'required') !== false) && !array_filter($data, function($a){return ($a !== null && $a !== '');})) {
				if(!array_key_exists('地区', $data) && $params->api_key == 'api_mobile_android'){
					\InstantCounter::count('fabubitian');
					throw new \Exception('属性' . $meta->displayName . '是必填项，请填写', '422');
				}
			}

			if ($meta->maxlength && mb_strlen($dataWithoutUnit) > $meta->maxlength) {
				throw new \Exception($meta->displayName .'不能超过' . $meta->maxlength . '个字', '423');
			}
			foreach ($data as $key => $value) {
				$ad->set($key, is_array($value) ? implode(',', $value) : (string)$value);
			}
		}
		if (!$ad->title) {
			throw new \Exception('属性标题是必填项，请填写', '422');
		}
		
		//Images
		if (is_string($params->images) && strlen($params->images)) $params->image = explode(',', $params->images);
		$images = self::validImage($params->image);
		$images = array_slice($images, 0, $user->imageLimit(\City::loadByName($params->cityEnglishName)));
		if ($ad->categoryEnglishName != 'ktvjiuba')	$ad->set('images', implode(" ", $images));
		else $ad->set('images', null);

		$ad->set('wanted', intval($params->wanted));

		$contacts = $ad->contacts();
		$ad->set('contacts', implode(' ', $contacts));

		$fingers = $ad->fingers();
		$ad->set('fingers', implode(' ', $fingers));
				
		$urls = $ad->urls();
		$ad->set('urls', implode(' ', $urls));
		$ad->set('fabuSessionId', intval($params->fabuSessionId));

		return $ad;
	}

	private static function validImage($images) {
		$validImages = array();
		if ($images && is_array($images)) {
			foreach ($images as $img) {
				$name = \Utility::convertImgUrlToFilename($img);
				if ($name)	$validImages[] = $name;
			}
		}
		return $validImages;
	}

	public static function loadAndCheck($adId, \User $user = null, $checkStatus = true, $udid = null) {
		$isValid = true;
		$ad = new \Ad();
		$ad->load($adId);
		if ($checkStatus && !$ad->editable()) {
			throw new \Exception('can not update deleted Ad', 504);
		}

		if ($user && $user->isFrozen()) {
			throw new \Exception('frozen owner', 503);
		}

		if ((!$user && $udid) || ($user && $ad->userId != $user->id && $user->type != \User::TYPE_SUPERMAN)){
			throw new \Exception('invalid owner for ad', 503);
		}

		return $ad;
	}

	private static function generateBanguiTips($lastOperation){
		$extra = '';
		if($lastOperation == '城市发布超限' || $lastOperation == '类目发布超限'){
			$extra = '请访问 http://www.baixing.com 使用付费产品。';
		}
		return strip_tags(\Bangui::description($lastOperation,false)) . $extra;
	}
	
	private static function ads($ads, $fields = null, $valueFlag = null, $onlyActive = true) {
		ini_set('memory_limit', '256M'); 
		$newAds = array();
		$fieldArray = explode(',', (string)$fields);
		foreach ($ads as $ad) {
			if (memory_get_usage() > 128 * 1024 * 1024) \Logger::info('memory limit', '', 'zhaojun@baixing.com');
			if ($onlyActive && $ad->status != \Ad::STATUS_ACTIVE) continue;
			if($ad->get('final_delete')){
				continue;//not return final deleted data
			}
			$newAd = array('link' => $ad->link(true));
			//if ($ad->status == \Ad::STATUS_PENDING || $ad->status == \Ad::STATUS_SUSPENDED){//违反版规
				if($ad->get('lastOperation')){//得到违反版规的具体提示
					$newAd['tips'] = self::generateBanguiTips($ad->get('lastOperation'));
				}	
			//}
			if (($mobile = $ad->mobile(false))) $newAd['mobile'] = $mobile;
			$basicAttributes = array('id', 'lat', 'lng', 'cityEnglishName', 'categoryEnglishName', 
				'categoryFirstLevelEnglishName', 'categoryNames',
				'areaCityLevelId', 'areaFirstLevelId', 'areaSecondLevelId', 'createdTime', 'wanted', 
				'userId', 'areaNames', 'status', 'imageFlag', 'mobileArea','lastOperation','postMethod', 'insertedTime');
			foreach ($basicAttributes as $attri)
				$newAd[$attri] = $ad->get($attri);
			
			//fix lat,lon == false, then crash client app
			if(isset($newAd['lat']) && $newAd['lat'] === false){
				$newAd['lat'] = 0;
			}
			
			if(isset($newAd['lng']) && $newAd['lng'] === false){
				$newAd['lng'] = 0;
			}

			try{
				$metas = $ad->category()->metas();
			}catch(\Exception $e){
				continue;
			}
			$metaValues = array();

			$attributes = $ad->attributes();

			foreach ($metas as $meta) {
				// if ($meta->name == '地区') continue;   //wap 站需要 地区_s 数据
				$name = trim($meta->displayName) && ($meta->name != '具体地点')? $meta->displayName : $meta->name; //具体地点的空格没法trim
				$value = $ad->get($meta->name);
				if ($meta instanceof \TreeMeta) {
					$label = self::adMetaValues($meta->link($value, $ad));
				} elseif ($meta->controlView == 'checkbox') {
					$singleLabel = array();
					foreach (explode(',', $value) as $singleValue) {
						$singleLabel[] = $meta->label($singleValue);
					}
					$label = join(',', $singleLabel);
				} else {
					$label = $meta->label($value);
				}
				if ($value) $newAd[$meta->name] = ($valueFlag && !in_array($meta->name, array('title', 'description', 'contact'))) ? compact('label', 'value') : $label;
				$newAd['attris'][$meta->name] = compact('name', 'label', 'value');
				if ($meta instanceof \TreeMeta && isset($attributes[$meta->name . '_s'])) {
					$newAd['attris'][$meta->name . '_s'] = $newAd['attris'][$meta->name];
					$newAd['attris'][$meta->name . '_s']['value'] = array();
					$objects = explode(' ', $attributes[$meta->name . '_s']);
					array_pop($objects);
					foreach ($objects as $objectId) {
						$newAd['attris'][$meta->name . '_s']['value'][$objectId] = $meta->label($objectId);
					}
					$tmpSubMetas = $newAd['attris'][$meta->name . '_s']['value'];
					if (is_array($tmpSubMetas)) {
						$subMetas = array();
						foreach($tmpSubMetas as $k => $val) {
							$subMetas[] = array($k => $val);
						}
						$newAd[$meta->name . '_s'] = $subMetas;
					}
				}
			}

			if ($ad->get('imageFlag')) {
				$newAd['images'] = array();
				$newAd['images']['square'] = $ad->images(\Ad::IMAGE_SIZE_SQUARE);
				$newAd['images']['small'] = $ad->images(\Ad::IMAGE_SIZE_SMALL);
				$newAd['images']['big'] = $ad->images(\Ad::IMAGE_SIZE_BIG);
				$newAd['images']['resize180'] = $ad->images(\Ad::IMAGE_RESIZE_180x180);
				$newAd['images']['resize140'] = $ad->images(\Ad::IMAGE_RESIZE_140x140);
				$newAd['images'] = json_decode(preg_replace('/img\d*\.baixing\.net/', 'tu.baixing.net', json_encode($newAd['images'])), true);
			}

			if (in_array('count', $fieldArray)) {
				$newAd['count'] = self::getAdCounter($ad->id);
			}

			if (count($fieldArray) > 1) {
				$filterAd = array();
				foreach ($fieldArray as $field) {
					if ($field && isset($newAd[$field])) $filterAd[$field] = $newAd[$field];
					if (strpos($field, 'images_') !== false && ($subkey = substr($field, 7)) && isset($newAd['images'][$subkey])) {
						$filterAd['images'][$subkey] = array_slice($newAd['images'][$subkey], 0, 5);
					}
				}
				$newAds[] = $filterAd;
			} else {
				$newAds[] = $newAd;
			}
		}

		// 在Ad详情里增加用户昵称属性 zhongjiawu@baixing.com
		if (count($newAds) > 0){
			$userIds = array();
			foreach ($newAds as $ad) {
				$userIds []= $ad['userId'];
			}
			
			$userLoader = new \User();
			$users = $userLoader->loads($userIds);
			$userIdNickMap = array();
			foreach ($users as $user) {
				$userIdNickMap [$user->id]= $user->nickname;
			}

			foreach ($newAds as &$ad){
				$userId = $ad['userId'];
				$ad['userNick'] = isset($userIdNickMap[$userId]) ? $userIdNickMap[$userId] : null;
			}
		}
		return $newAds;
	}
	
	private static function adMetaValues($link) {
		$link = str_replace(" - ", " ", $link);
		return trim(strip_tags($link));
	}

	public static function getAdCounter($adId) {
		$res = \Http::getUrl("http://counter.baixing.net/c.php?id={$adId}&noadd=1", 1);
		return (preg_match('/(\d+)\)$/', $res, $m)) ? $m[1] : 0;
	}

	private static function searchOption(\ApiParam $params, $sort = null) {
		$params->query = $params->query ? trim($params->query) : null;
		
		$query = $params->query ? new \RawQuery($params->query) : new \TrueQuery();
		if (isset($params['keyword']) && strlen(trim($params['keyword'])) > 0)
		{
			$keyword = trim($params['keyword']);
			$query = new \AndQuery($query, new \Query('title', $keyword));
		}
		$options = array();
		
		if ($params->cache) 
			$options['cache'] = 1;
		
		$rows = intval($params->rows);
		$options['rows'] = ($rows && $rows <= 1000 && $rows >= 0) ? $rows : '10';
		$start = $params->start;
		$options['start'] = ($start && $start <= 100000 && $start >= 0) ? $start : '0';
		if ($sort) $options['sort'] = $sort;

		if ($params->d) $options['d'] = $params->d;
		
		$category = $params->categoryEnglishName ? \Category::loadByName($params->categoryEnglishName) : null;
		$city = $params->cityEnglishName ? \City::loadByName ($params->cityEnglishName) : null;
		return array($query, $category, $city, $params->wanted, $options, $params->rt);
	}
}
?>
