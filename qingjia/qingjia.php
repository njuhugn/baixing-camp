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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-paginator.js"></script>
    <script src="js/bootstrap-datetimepicker.min.js"></script>
    <script src="js/check.js" type="text/javascript"></script>
</script>
</head>
<body>
<div>
    <form class="form-horizontal" id="myForm" method="post" action="qingjia.php" onsubmit="return validate_form(this)">
        <legend>我要请假</legend>
        <div class="control-group">
            <label class="control-label" for="inputName">姓名</label>
            <div class="controls">
                <input type="text" id="inputEmail" name="name" placeholder="姓名">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputManager">经理</label>
            <div class="controls">
                <input type="text" id="inputManager" name="manager" placeholder="经理">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputReason">原因</label>
            <div class="controls">
                <input type="text" id="inputReason" name="reason" placeholder="原因">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputDate">日期</label>
            <div class="controls">
            <div class="input-append date form_datetime">
                <input size="16" type="text" value="" id="inputDate" name="date" placeholder="日期">
                <span class="add-on"><i class="icon-th"></i></span>
            </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputDays">天数</label>
            <div class="controls">
                <input type="text" id="inputDays" name="days" placeholder="天数">
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
<script type="text/javascript">
$(".form_datetime").datetimepicker({
        format: "yyyy-mm-dd",
        minView: 'month',
        autoclose: true,
    });
    var options = {
            currentPage: <?php echo $page['current'] ?>,
            totalPages:  <?php echo $page['total'] ?>,
            onPageChanged: function(e, oldPage, newPage){
                $('#pagetxt').load("list.php?page=" + newPage);
            }
    }
    $('#example').bootstrapPaginator(options);
</script>
</body>
</html>