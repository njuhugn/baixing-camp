<?php

define('SIZE', 16);
define('MYSQL_HOST', '192.168.2.19');
define('MYSQL_USER', 'techsummer');
define('MYSQL_PASS', 'techsummer');
define('MYSQL_DB', 'techsummer');
define('MYSQL_TABLE', 't1_qingjia');

$con = null;
$page = null;
$list = array();
for ($i = 0; $i < SIZE * 5; $i++)
{
	$tuple = array();
	$tuple['qj_id'] = $i;
	$tuple['qj_name'] = "random";
	$tuple['qj_manager'] = "kaola";
	$tuple['qj_date'] = "2013-09-10";
	$tuple['qj_days'] = $i + 1;
	$tuple['qj_reason'] = "no reasons";
	$list[] = $tuple;
}

function getConnection()
{
	global $con;
	$con = isset($con) ? $con : mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) or die(mysql_error());
	mysql_select_db(MYSQL_DB) or die(mysql_error());
	mysql_query("set names utf8") or die(mysql_error());
	return $con;
}

function getPage()
{
	/*$con = getConnection();
	$sql = "select count(*) from " . MYSQL_TABLE;
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_row($res);
	$total = $row[0];*/
	$page = array();
	$total = SIZE * 5;
	$page['total'] = ceil($total/SIZE);
	if(!isset($_GET['page']) || !intval($_GET['page']) || $_GET['page']>$total)
		$page['current'] = 1;
	else
		$page['current'] = $_GET['page'];
	$page['prev'] = $page['current'] - 1;
	$page['next'] = $page['current'] + 1;
	return $page;
}

function getQingjia($page)
{
	$startnum = ($page - 1) * SIZE;
	/*
	$sql = "select * from t1_qingjia order by qj_date desc limit $startnum," . SIZE;
	$res = mysql_query($sql) or die(mysql_error());
	$ret = array();
	while ($row = mysql_fetch_assoc($res))
	{
		$ret[] = $row;
	}*/
	$ret = array();
	global $list;
	for ($i = $startnum; $i < $startnum + SIZE; $i++)
	{
		$ret[] = $list[$i];
	}
	return $ret;
}

function yaoQingjia()
{
	/*
	$con = getConnection();
	$sql = "insert into t1_qingjia (qj_id, qj_name, qj_manager, qj_date, qj_days, qj_reason) values ('', '$_POST[name]', '$_POST[manager]', '$_POST[date]', '$_POST[days]', '$_POST[reason]')";
	mysql_query($sql, $con) or die(mysql_error());
	$id = mysql_insert_id();
	mysql_close($con);
	return $id;
	*/
	$tuple = array();
	$tuple['qj_id'] = 111;
	$tuple['qj_name'] = $_POST['name'];
	$tuple['qj_manager'] = $_POST['manager'];
	$tuple['qj_date'] = $_POST['date'];
	$tuple['qj_days'] = $_POST['days'];
	$tuple['qj_reason'] = $_POST['reason'];
	var_dump($tuple);
}

switch($_SERVER['REQUEST_METHOD'])
{
case 'GET':
	break;
case 'POST':
	yaoQingjia();
	break;
default:
	break;
}

?>

<html>
<head>
	<meta charset="utf-8" />
	<title>请假系统</title>
</head>
<body>
	<form action="qingjia.php" method="post" accept-charset="utf-8">
		请假员工：<input type="text" name="name"/><br />
		Manager： <input type="text" name="manager"/><br />
		请假时间：<input type="text" name="date"/><br />
		请假天数：<input type="text" name="days"/><br />
		请假原因：<input type="text" name="reason"/><br />
		<input type="submit" /><br />
	</form>
	<table>
		<caption>请假条</caption>
		<tr>
			<th>员工</th>
			<th>Manager</th>
			<th>时间</th>
			<th>天数</th>
			<th>原因</th>
		</tr>
		<?php
			$page = getPage();
			foreach (getQingjia($page['current']) as $tuple)
			{
		?>
		<tr>
			<td><?=$tuple['qj_name']?></td>
			<td><?=$tuple['qj_manager']?></td>
			<td><?=$tuple['qj_date']?></td>
			<td><?=$tuple['qj_days']?></td>
			<td><?=$tuple['qj_reason']?></td>
		</tr>
		<?php
			}
		?>
	</table>
</body>
</html>