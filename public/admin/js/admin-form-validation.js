/** Display error message using field name */
$.validator.messages.required = function(param, input) {
	var input_name = input.name.replace(/\_id$/, '');
	return 'The ' + input_name.replace(/\_/g, ' ') + ' field is required';
}

//NUMBER ONLY CLASS
$('.number_only').bind('keyup paste', function() {
	this.value = this.value.replace(/[^0-9]/g, '');
});

$('.currency_number_only').bind('keyup paste', function() {
	this.value = this.value.replace(/[^0-9\.]/g, '');
});

//LETTERS ONLY
jQuery.validator.addMethod("lettersonlys", function(value, element) {
	return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
}, "Letters only please");

//GREATER THAN
$.validator.addMethod("greaterThan", function(value, element, param) {
	var $otherElement = $(param);
	return parseInt(value, 10) > parseInt($otherElement.val(), 10);
}, "This field should be greater!");

//disable space from password 
$('.disabledspace').keypress(function(e) {
	if (e.which === 32) {
		return false;
	}
});
//disable first input space
$('.disablefirstspace').keypress(function(e) {
	if (e.which === 32 && !this.value.length) {
		return false;
	}
});


$.validator.addMethod(
        "usaDate",
        function(value, element) {
            if(validateDate(value) == true){
                return true;
            }
        },
        "Please enter a date in the format dd-mm-yyyy."
    );


jQuery.validator.addMethod("customDateTimeValidation", function(value, element) {
    var selectedDateTime = $(".datepicker").val();
    var time = convertTime12to24($(".timepicker").val());
    var selectedDate_time = selectedDateTime+' '+time;
    var D1 = selectedDate_time;
    var timeSplit = time.split(':');
    dateFirst = D1.split('-');
    var value = new Date(parseInt(dateFirst[2]), (parseInt(dateFirst[1])-1), parseInt(dateFirst[0]),parseInt(timeSplit[0]),parseInt(timeSplit[1]),parseInt('00')); //Year, Month, Date
    if(value.getTime() <= new Date().getTime())
    {
    	console.log('getTime--'+ value.getTime());
    	console.log('Now--'+ new Date().getTime());

        return false;
    }
    return true;
 }, "Selected time must be greater then current time");



function validateDate(str) {
    var match = /(^(((0[1-9]|1[0-9]|2[0-8])[-](0[1-9]|1[012]))|((29|30|31)[-](0[13578]|1[02]))|((29|30)[-](0[4,6,9]|11)))[-](19|[2-9][0-9])\d\d$)|(^29[-]02[-](19|[2-9][0-9])(00|04|08|12|16|20|24|28|32|36|40|44|48|52|56|60|64|68|72|76|80|84|88|92|96)$)/;
    return match.test(str);       
  }

$(document).on('keyup', 'input', function(e) {
	if ($.trim($(this).val()) == "") {
		$(this).val('');
	}
});
$(document).on('blur', 'input', function(e) {
	if ($.trim($(this).val()) == "") {
		$(this).val('');
	}
});

$(document).on('keyup', 'textarea', function(e) {
	if ($.trim($(this).val()) == "") {
		$(this).val('');
	}
});
$(document).on('blur', 'textarea', function(e) {
	if ($.trim($(this).val()) == "") {
		$(this).val('');
	}
});

jQuery.validator.addMethod("specialChars", function( value, element ) {
    var regex = new RegExp("/^[a-z0-9\-\s]+$/i");
    var key = value;

    if (!regex.test(key)) {
       return false;
    }
    return true;
}, "please use only alphanumeric or alphabetic characters");


$.validator.addMethod("loginRegex", function(value, element) {
    return this.optional(element) || /^[a-z0-9\s]+$/i.test(value);
}, "Title must contain only letters, numbers.");


/* Email Validation Add Method */
jQuery.validator
		.addMethod(
				"emailfull",
				function(value, element) {
					return this.optional(element)
							|| /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i
									.test(value);
				}, "Please enter valid email address.");

jQuery.validator.addMethod("noSpace", function(value, element) {
	if (value.trim() != "") {
		return true;
	}

}, "This field is required.");




$("#admin_edit_profile_form").validate({
	errorElement : 'span',
	rules : {
		full_name : {
			required : true,
			loginRegex: true,
			maxlength : 50
		},
		email : {
			required : true,
			emailfull:true
		},
		mobile_number : {
			number:true,
			maxlength:16,
			minlength:6
		},
		password : {
			maxlength:15,
			minlength:6
		},
	},
	messages:{
		contact_number:{
			minlength:"The contact number must be at least  6 digits.",
			maxlength:"The contact number must not be greater than 16 digits." 
		}
	},
});


$("#add_service_category_form").validate({
	errorElement : 'span',
	rules : {
		name : {
			required : true,
			loginRegex: true,
			maxlength : 50
		},
		service_category_id : {
			required : true,
		},
		file_path : {
			required : true,
            extension: "jpg,jpeg,png,jpeg,gif"
		},
		
	},
	/* use below section if required to place the error*/
	errorPlacement : function(error, element) {
		if (element.attr("name") == "service_category_id") {
			$("#service_category_id_error").text(error.html());
		}
		else if (element.attr("name") == "file_path") {
			$("#sub_cat_img_error").text(error.html());
		}else {
			error.insertAfter(element);
		}
	},
	messages:{
		name:{
			required:"The category name field is required.",
		},
		service_category_id:{
			required:"The category type field is required.",
		},
		file_path:{
			required:"The category image field is required.",
		}
	},
});

$("#update_service_category_form").validate({
	errorElement : 'span',
	rules : {
		name : {
			required : true,
			loginRegex: true,
			maxlength : 50
		},
		service_category_id : {
			required : true,
		},
	},
	/* use below section if required to place the error*/
	errorPlacement : function(error, element) {
		if (element.attr("name") == "service_category_id") {
			$("#service_category_id_error").text(error.html());
		} else {
			error.insertAfter(element);
		}
	},
	messages:{
		name:{
			required:"The category name field is required.",
		},
		service_category_id:{
			required:"The category type field is required.",
		}
	},
});

$('form input,select').change(function(e){
	if($("#category_drop_down").find(":selected").text()){
		$("#service_category_id_error").text('');
	}
	$(this).closest('form').valid();
});

