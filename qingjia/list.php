<?php

require_once('func.php');

?>

<table class="table table-bordered table-striped table-hover">
	<caption><h3>员工请假信息</h3></caption>
	<thead>
		<tr>
			<th>姓名</th>
			<th>经理</th>
			<th>原因</th>
			<th>日期</th>
			<th>天数</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$page = getPage();
			foreach (getQingjia($page['current']) as $tuple)
			{
		?>
		<tr>
			<td style="width:20%"><?=htmlspecialchars($tuple['qj_name'])?></td>
			<td style="width:20%"><?=htmlspecialchars($tuple['qj_manager'])?></td>
			<td style="width:20%"><?=htmlspecialchars($tuple['qj_reason'])?></td>
			<td style="width:20%"><?=htmlspecialchars($tuple['qj_date'])?></td>
			<td style="width:20%"><?=htmlspecialchars($tuple['qj_days'])?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>