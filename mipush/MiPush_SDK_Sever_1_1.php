<?php
	$app_secret = 'kHMlTOFtX2E8Ws0YQ7H2ENGYr2o9COxUFJ7+XXgnf6o=';
	
	//sandbox url:
	$url = 'https://sandbox.xmpush.xiaomi.com/v1/send';
	// production url:
	//$url = 'https://api.xmpush.xiaomi.com/v1/send';
	
	// @payload
	$message = 'This is a test message.';
	
	// @registration_id
	$registrationId = '24gNhuyWPy6ZcgXS6OgsRankidUXsByFpGGEaP35i+4O5uaRF7NH0jRLRAtxgoMgNtkzJDaBjZNAGRHFqb4icQ==';
	
	// @ttl, in seconds, how long the server keep it, by default is 0, means 2 weeks.
	$ttl = 0;
	
	// @restricted_package_name, your package name register in developer site.
	$restricted_package_name = 'com.baixing.quanleimu';
	
	// @alias
	$alias = 'alias';
	
	// @topic
	$topic = 'topic';
	
	function send($message, $registrationId, $ttl, $restricted_package_name, $app_secret, $url)
	{
		if(empty($message) || empty($registrationId) || empty($restricted_package_name) || empty($app_secret) || empty($url) || !is_int($ttl))
			echo 'wrong parameter';
		else
		{
			$fields = array('payload' => $message, 'registration_id' => $registrationId, 'ttl' => $ttl, 'restricted_package_name' => $restricted_package_name);
			$headers = array('Authorization: key=' . $app_secret, 'Content-Type: application/x-www-form-urlencoded');
			
			// Open connection
			$ch = curl_init();

			// Set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
			
			// Execute post
			$result = curl_exec($ch);
			
			// Close connection
			curl_close($ch);
			echo $result;
		}
	}
	
	function sendToAlias($message, $registrationId, $ttl, $restricted_package_name, $alias, $app_secret, $url)
	{
		if(empty($message) || empty($registrationId) || empty($restricted_package_name) || empty($alias) || empty($app_secret) || empty($url) || !is_int($ttl))
			echo 'wrong parameter';
		else
		{
			$fields = array('payload' => $message, 'registration_id' => $registrationId, 'ttl' => $ttl, 'restricted_package_name' => $restricted_package_name, 'alias' => $alias);
			$headers = array('Authorization: key=' . $app_secret, 'Content-Type: application/x-www-form-urlencoded');
			
			// Open connection
			$ch = curl_init();

			// Set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
			
			// Execute post
			$result = curl_exec($ch);
			
			// Close connection
			curl_close($ch);
			echo $result;
		}
	}
	
	function broadcast($message, $registrationId, $ttl, $restricted_package_name, $topic, $app_secret, $url)
	{
		if(empty($message) || empty($registrationId) || empty($restricted_package_name) || empty($topic) || empty($app_secret) || empty($url) || !is_int($ttl))
			echo 'wrong parameter';
		else
		{
			$fields = array('payload' => $message, 'registration_id' => $registrationId, 'ttl' => $ttl, 'restricted_package_name' => $restricted_package_name, 'topic' => $topic);
			$headers = array('Authorization: key=' . $app_secret, 'Content-Type: application/x-www-form-urlencoded');
			
			// Open connection
			$ch = curl_init();

			// Set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
			
			// Execute post
			$result = curl_exec($ch);
			
			// Close connection
			curl_close($ch);
			echo $result;
		}
	}
	
	//demo
	
    send($message, $registrationId, $ttl, $restricted_package_name, $app_secret, $url);
    // The result is JSON string, include fields:
    //   "result": string, "ok" for success, "error" for failed;
    //   "description": string, short description for the error code;
    //   "data": sub-json object for success, contains detail result;
    //   "code": integer, 0 for success; others for failed;
    //   "info": string, detail error message;
    //
    // examples:
    // If send message success, the "code" is 0, and data contains message id:
    //   {"result":"ok","description":"成功","data":{"id":"1000999_1375164696370"},"code":0,"info":"Received push messages for 1 regid"}
    // If failed, the "code" is positive integer, the reason is the detail message for why failed, such as
    //   {"result":"error","reason":"Invalid package name.","description":"参数值非法","code":10017}

	echo '<br>';
    //sendToAlias($message, $registrationId, $ttl, $restricted_package_name, $alias, $app_secret, $url);
    // If send to alias success:
    //   {"result":"ok","description":"成功","data":{"id":"2000999_1375173912006"},"code":0,"info":"Received push messages for 1 alias"}
    // If the payload is empty, send to alias failed, then return
    //  {"result":"error","reason":"payload参数不能为空","description":"缺失必选参数","code":10016} 

	echo '<br>';
	//broadcast($message, $registrationId, $ttl, $restricted_package_name, $topic, $app_secret, $url);
    //   {"result":"ok","description":"成功","data":{"id":"3000999_1375173914635"},"code":0,"info":"Received push messages for 1 topic"}
