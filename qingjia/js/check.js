function validate_required(field,alerttxt){
	with (field){
		if (value==null||value==""){
			alert(alerttxt);
			return false;
		}
		else {
			return true;
		}
	}
}

function validate_required_positive(field,alerttxt){
	with(field) {
		//console.log("days: ", value);
		var pat = /^\d+(\.\d+)?$/;
		var re = new RegExp(pat);
		var t = re.test(value);
		if (!t) alert(alerttxt);
		//console.log(t);
		return t;
	}
}

function validate_required_format(field,alerttxt){
	with(field) {
		var pat = /^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])$/;
		var re = new RegExp(pat);
		var t = re.test(value);
		if (!t) alert(alerttxt);
		return t;
	}
}

function validate_form(thisform){
	//console.log("submit");
	with (thisform){
		if (validate_required(name,"Name must be filled out!")===false){
			name.focus();
			return false;
		}else if(validate_required(manager,"Manager must be filled out!")===false){
			manager.focus();
			return false;
		}else if(validate_required(reason,"Reason must be filled out!")===false){
			reason.focus();
			return false;
		}else if(validate_required(date,"Date must be filled out!")===false||validate_required_format(date, "Date format error!")===false){
			date.focus();
			return false;
		}else if(validate_required(days,"Days must be filled out!")===false||validate_required_positive(days,"Days must be a positive number!")===false){
			days.focus();
			return false;
		}
		else {
			//console.log("form: ok");
			return true;
		}
	}
}