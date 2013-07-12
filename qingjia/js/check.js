$(document).ready(function() {

var metrics = [
	['#inputName', 'presence', "谁请假？"],
	['#inputManager', 'presence', "没人管你？"],
	['#inputReason', 'presence', "没有理由怎么批假？"],
	['#inputDays', /^\d+(\.\d+)?$/, "天数应该大于0吧？"],
	['#inputDate', /^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])$/, "这是哪里的历法？"]
];

$('#myForm').nod(metrics);
//console.log("ok");

});