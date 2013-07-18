<?php
//yangminda@baixing.com
namespace Api;
class Email extends \Api implements \ApiLimitation, \ApiList {
	public static function apiList() {
		$apiList = array();
		$apiList['email.send'] = array(
			'require'	=> array('to', 'subject', 'body'),
			'optional'	=> array('from', 'fromAlias'),
			'return'	=> \ApiList::API_RETURN_BOOL,
			'type'		=> \ApiList::API_TYPE_WRITE,
		);
		return $apiList;
	}

	public static function limitation(\ApiParam $params) {
		return;
	}
	
	public static function email_send(\ApiParam $params) {
		if (!$params->to) throw new \Exception('Email target required！', 501);
		if (!\Email::isValidEmail($params->to)) throw new \Exception('Email target invalid！', 502);		
		if (!$params->subject) throw new \Exception('Email subject required！', 503);
		if (!$params->body) throw new \Exception('Email content required！', 504);
		if ($params->from && !\Email::isValidEmail($params->from)) throw new \Exception('Email source invalid', 505);
		return (bool)\Email::sendMail($params->to, $params->subject, $params->body, $params->from, $params->fromAlias);
	}
}
?>