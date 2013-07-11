<?php
require_once('func.php');
?>

<html>
<head>
	<meta charset="utf-8" />
	<title>请假系统</title>
</head>
<body>
	<form action="func.php" method="post" accept-charset="utf-8">
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
			foreach (getQingjia() as $tuple)
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