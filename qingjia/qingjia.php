<?php

require_once('func.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST')
	yaoQingjia();

?>

<!DOCTYPE html>
<html>
<head>
	<title>请假系统</title>
	<meta charset="utf-8" />
	<link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/datetimepicker.css" rel="stylesheet" />
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-paginator.js"></script>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.zh-CN.js"></script>
    <script type="text/javascript" src="js/check.js"></script>
    <script type="text/javascript" src="js/nod.js"></script>
</script>
</head>
<body>
<div>
    <form class="form-horizontal" id="myForm" method="post" action="qingjia.php">
        <legend><h2>我要请假</h2></legend>
        <div class="control-group">
            <label class="control-label" for="inputName">申请人</label>
            <div class="controls">
                <input type="text" id="inputName" name="name" placeholder="申请人Email">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputManager">Manager</label>
            <div class="controls">
                <input type="text" id="inputManager" name="manager" placeholder="Manager Email">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputReason">原因</label>
            <div class="controls">
                <input type="text" id="inputReason" name="reason" placeholder="为什么休假">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputDate">日期</label>
            <div class="controls">
                <input class="form_datetime" type="text" value="" id="inputDate" name="date" placeholder="从哪天开始休假">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputDays">天数</label>
            <div class="controls">
                <input type="text" id="inputDays" name="days" placeholder="休假几天">
            </div>
        </div>
         <div class="control-group">
            <div class="controls">
                <button type="submit" class="btn">提交</button>
            </div>
        </div>
    </form>
    <div id="pagetxt">
        <?php require_once('list.php'); ?>
    </div>
    <div id="example"></div>
</div>
<footer>
    &copy; 2013 百姓网
    <div style="float:right;">前端：郑捷凯，何文琦；后端：张志齐，曾劲</div>
</footer>
<script type="text/javascript">
$(".form_datetime").datetimepicker({
        format: "yyyy-mm-dd",
        minView: 'month',
        autoclose: true,
        language: 'zh-CN',
    });
var options = {
        currentPage: <?php echo htmlspecialchars($page['current']) ?>,
        totalPages:  <?php echo htmlspecialchars($page['total']) ?>,
        onPageChanged: function(e, oldPage, newPage){
            $('#pagetxt').load("list.php?page=" + newPage);
        }
}
$('#example').bootstrapPaginator(options);
</script>
</body>
</html>