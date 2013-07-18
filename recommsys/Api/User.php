<?php
//zhaojun@baixing.com
namespace Api;
class User extends \Api implements \ApiLimitation, \ApiList {
	public static function apiList() {
		$apiList = array();
		//write
		$apiList['user.register'] = array(
			'require'	=> array('mobile', 'password'),
			'optional'	=> array('nickname'),
			'return'	=> \ApiList::API_RETURN_STRING,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		$apiList['user.update'] = array(
			'require'	=> array('mobile', 'userToken', 'nickname'),
			'return'	=> \ApiList::API_RETURN_BOOL,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);

		//read
		$apiList['user.show'] = array(
			'require'	=> array('mobile', 'userToken'),
			'optional'	=> array('nickname'),
			'return'	=> \ApiList::API_RETURN_BOOL,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		
		
		//read
		$apiList['user.profile'] = array(
			'require'	=> array('userid'),
			'optional'	=> array(),
			'return'	=> \ApiList::API_RETURN_ARRAY,
			'type'		=> \ApiList::API_TYPE_READ,
		);
		return $apiList;
	}

	public static function limitation(\ApiParam $params) {
		return;
	}

	public static function user_register(\ApiParam $params) {
		if ($params->nickname && $params->password) {
			$user = self::getUserByNickname($params->nickname);
			if (!$user) throw new \Exception('用户不存在', 505);
			if (!$user->isMatchedPassword($params->password)) throw new \Exception('密码错误', 505);
		} else {
			if (!$params->mobile || !$params->password) throw new \Exception('无效的帐号或密码', 503);
			$user = self::autoRegUser($params->mobile, $params->password);
		}
		return $user->id;
	}

	public static function user_update(\ApiParam $params) {
		$user = self::user_show($params);
		if ($nickname = $params->nickname) {
			if (($validNick = \User::isValidNickname($nickname)) != 'Valid') throw new \Exception($validNick, 523);
			if ($nickname != $user->nickname && !\User::isNicknameAvailable($nickname)) throw new \Exception('用户名已经被注册了', 524);
			$user->nickname = $nickname;
		}
		foreach (array('email', 'description', '公司名称', '公司介绍') as $key) {
			if ($params->$key) $user->set($key, $params->$key);
		}
		$user->save();
		return true;
	}

	public static function user_show(\ApiParam $params) {
		$user = \User::loadByMobile($params->mobile);
		if (!$user && $params->nickname) {
			$user = self::getUserByNickname($params->nickname);
		}
		if (!$user || md5($user->password . self::getApiSecret($params->api_key)) != $params->userToken) {
			throw new \Exception('帐号或密码错误，请重新登录！', 503);
		}
		if ($user->isFrozen()) {
			$date = date('Y年m月d日', $user->frozenUntil);
			$log = \UserFreezeLog::getLast($user->id);
			$rule = $log ? $log->rule : '违规操作';
			throw new \Exception("该账户由于“{$rule}”已被冻结，请于{$date}后再尝试登录。", 503);
		}
		$params->uid = $user->id;
		return $user;
	}
	
	public static function user_profile(\ApiParam $params) {
		$result = array();
		if(!$params->userid)
		{
			throw new \Exception('请指定用户ID － userid', 999);
			return null;
		}
		$user = new \User();
		$user->load($params->userid);
		if($user != null){
			$result['userid'] = $user->id;
			$result['mobile'] = $user->mobile;
			$result['nickname'] = $user->nickname;
			//以后加入更多属性
		} 
		return $result;
	}

	private static function autoRegUser($mobile, $password){
		if (!\Validate::isMobile($mobile)) throw new \Exception('手机号码不对', 505);
		if (strlen($password) == 0) throw new \Exception('密码错误', 505);
		$user = \User::loadByMobile($mobile);
		if ($user){
			if (!$user->isMatchedPassword($password)) throw new \Exception('密码错误', 505);
		} else {
			try{
				$user = \Authorization::registerAutoUserNick($password, 'API_'.substr(time(),-6).rand(1000,9999), $mobile);
			} catch (Exception $e) {
				 throw new \Exception('新建用户失败', 505);
			}
		}
		return $user;
	}

	/**
	 * @param $nickname
	 * @return \User
	 */
	private static function getUserByNickname($nickname) {
		$loader = new \User();
		$loader->nickname = $nickname;
		return reset($loader->find());
	}
}
?>