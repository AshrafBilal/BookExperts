$(document).ready(function() {
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	


	
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

	//Put our input DOM element into a jQuery Object
	var $jqDate = jQuery('.card-expiry-date');

	//Bind keyup/keydown to the input
	$jqDate.bind('keyup paste','keydown', function(e){

	  //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
	    if(e.which !== 8) { 
	        var numChars = $jqDate.val().length;
	        if(numChars === 2){
	            var thisVal = $jqDate.val();
	            thisVal += '/';
	            $jqDate.val(thisVal);
	        }
	  }
	});

	//LETTERS ONLY
	jQuery.validator.addMethod("lettersonlys", function(value, element) {
		return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
	}, "Letters only please");

	jQuery.validator.addMethod("imagearray", function(value, element) {
		var validFields = $('input[name="images[]"]').map(function() {
		    if ($(this).val() != "") {
		        return $(this);
		    }
		}).get();    

		if (validFields.length) {
		    console.log("Form is valid");
		} else {
		    console.log("Form is not valid");
		}
	}, "Please select atleast one image");

	//GREATER THAN
	$.validator.addMethod("greaterThan", function(value, element, param) {
		var $otherElement = $(param);
		return parseInt(value, 10) > parseInt($otherElement.val(), 10);
	}, "This field should be greater!"
	);

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

	/* Email Validation Add Method */
	jQuery.validator.addMethod("emailfull", function(value, element) {
		return this.optional(element) || /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i.test(value);
	}, "Please enter valid email address.");

	jQuery.validator.addMethod("noSpace", function(value, element) {
		if (value.trim() != "") {
			return true;
		}

	}, "This field is required.");

	
    
    $("#admin_login_form").validate({
    	errorElement : 'span',
    	 rules: {
   	      email: {
   	         required: true,
   	      emailfull:true
   	     },
	   	  password: {
		         required: true,
		     }
   	  },
     });

});