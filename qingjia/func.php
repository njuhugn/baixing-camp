<?php

require_once('const.php');

class DataConnection {
	private static $connection = null;

	public static function getConnection() {
		if (self::$connection === null) {
			self::$connection = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) or die(mysql_error());
			mysql_select_db(MYSQL_DB) or die(mysql_error());
			mysql_query('set names utf8') or die(mysql_error());
		}
		return self::$connection;
	}

	public static function closeConnection() {
		if (isset(self::$connection))
			mysql_close(self::$connection) or die(mysql_error());
		self::$connection = null;
	}
}

function getPage()
{
	$sql = "select count(*) from " . MYSQL_TABLE;
	$con = DataConnection::getConnection();
	$res = mysql_query($sql, $con) or die(mysql_error());
	$row = mysql_fetch_row($res);
	$total = $row[0];
	$page = array();
	$page['total'] = ceil($total / SIZE);
	if(!isset($_GET['page']) || !intval($_GET['page']) || $_GET['page']>$total)
		$page['current'] = 1;
	else
		$page['current'] = mysql_real_escape_string($_GET['page']);
	return $page;
}

function getQingjia($page)
{
	$startnum = ($page - 1) * SIZE;
	$sql = "select * from t1_qingjia order by qj_id desc limit $startnum," . SIZE;
	$con = DataConnection::getConnection();
	$res = mysql_query($sql, $con) or die(mysql_error());
	$ret = array();
	while ($row = mysql_fetch_assoc($res))
		$ret[] = $row;
	return $ret;
}

function yaoQingjia()
{
	$name = mysql_real_escape_string($_POST['name']);
	$manager = mysql_real_escape_string($_POST['manager']);
	$date = mysql_real_escape_string($_POST['date']);
	$days = mysql_real_escape_string($_POST['days']);
	$reason = mysql_real_escape_string($_POST['reason']);
	$sql = "insert into t1_qingjia (qj_id, qj_name, qj_manager, qj_date, qj_days, qj_reason) values ('', '$name', '$manager', '$date', '$days', '$reason')";
	$con = DataConnection::getConnection();
	mysql_query($sql, $con) or die(mysql_error());
	$id = mysql_insert_id();
	return $id;
}