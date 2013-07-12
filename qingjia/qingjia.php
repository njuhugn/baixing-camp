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
    <link href="css/validationEngine.jquery.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-paginator.js"></script>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.zh-CN.js"></script>
    <script type="text/javascript" src="js/jquery.validationEngine-zh_CN.js"></script>
    <script type="text/javascript" src="js/jquery.validationEngine.js"></script>
</script>
</head>
<body>
<div>
    <form class="form-horizontal" id="myForm" method="post" action="qingjia.php">
        <legend><h2>我要请假</h2></legend>
        <div class="control-group">
            <label class="control-label" for="inputName">申请人</label>
            <div class="controls">
                <input type="text" id="inputName" name="name" placeholder="申请人Email" class="validate[required,custom[email]]" />
                <input type="submit" style="visibility: hidden;" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputManager">Manager</label>
            <div class="controls">
                <input type="text" id="inputManager" name="manager" placeholder="Manager Email" class="validate[required,custom[email]]" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputReason">原因</label>
            <div class="controls">
                <input type="text" id="inputReason" name="reason" placeholder="为什么休假" class="validate[required]" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputDate">日期</label>
            <div class="controls">
                <input class="form_datetime" type="text" value="" id="inputDate" name="date" placeholder="从哪天开始休假" class="validate[required,custom[date],future[NOW]]"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputDays">天数</label>
            <div class="controls">
                <input type="text" id="inputDays" name="days" placeholder="休假几天" class="validate[required,custom[number,min[0.1]]"/>
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

$(document).ready(function() {
    $("#myForm").validationEngine();
});
</script>

</body>
</html>