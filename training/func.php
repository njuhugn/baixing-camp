<?php

define('SIZE', 16);
define('MYSQL_HOST', '192.168.2.19');
define('MYSQL_USER', 'techsummer');
define('MYSQL_PASS', 'techsummer');
define('MYSQL_DB', 'techsummer');
define('MYSQL_TABLE', 't1_qingjia');

$con = null;

function getConnection()
{
	global $con;
	$con = isset($con) ? $con : mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) or die(mysql_error());
	mysql_select_db(MYSQL_DB) or die(mysql_error());
	mysql_query("set names utf8") or die(mysql_error());
	return $con;
}

function getQingjia()
{
	$con = getConnection();
	$sql = "select count(*) from " . MYSQL_TABLE;
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_row($res);
	$total = $row[0];
	$pages = ceil($total/SIZE);
	if(!isset($_GET['page']) || !intval($_GET['page']) || $_GET['page']>$total)
		$page = 1;
	else
		$page = $_GET['page'];
	$startnum = ($page - 1) * SIZE;
	$sql = "select * from t1_qingjia order by qj_date desc limit $startnum," . SIZE;
	$res = mysql_query($sql) or die(mysql_error());
	$list = array();
	while ($row = mysql_fetch_assoc($res))
	{
		$list[] = $row;
	}
	return $list;
}

function yaoQingjia()
{
	$con = getConnection();
	$sql = "insert into t1_qingjia (qj_id, qj_name, qj_manager, qj_date, qj_days, qj_reason) values ('', '$_POST[name]', '$_POST[manager]', '$_POST[date]', '$_POST[days]', '$_POST[reason]')";
	mysql_query($sql, $con) or die(mysql_error());
	$id = mysql_insert_id();
	mysql_close($con);
	return $id;
}

switch($_SERVER['REQUEST_METHOD'])
{
case 'GET':
	break;
case 'POST':
	yaoQingjia();
	header("Location: qingjia.php");
	break;
default:
	break;
}