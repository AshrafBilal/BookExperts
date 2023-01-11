var site_url = window.location.protocol + '//' + window.location.host;
$(document).ready(function() {
	
	
	
   
   /*$(document).on('click', '.delete-datatable-record', function(e){
    	$('#service_category_id').val($(this).attr('data-id'));
		Swal.fire({
			text: "Are you sure you want to delete this Service Category?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete !'
		}).then((result) => {
			if (result.isConfirmed) {
				
				$("#"+$(this).attr('delete_form')).submit();
			}
		})
    });*/
   
  /* $(document).on('click', '.delete-users-record', function(e){
   	$('#customer_id').val($(this).attr('data-id'));
		Swal.fire({
			text: "Are you sure you want to delete this Customer?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete !'
		}).then((result) => {
			if (result.isConfirmed) {
				
				$("#"+$(this).attr('delete_form')).submit();
			}
		})
   });*/


	
$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});


$("#profile_input_file").change(function() {
    var filePath = $(this).val();
    let allowedExtensions = /(\.jpg|\.jpeg|\.png|\.jpeg|\.gif)$/i; 
	 if (!allowedExtensions.exec(filePath)) { 
         $(this).val(''); 
         Swal.fire({
        		text: "Invalid file type",
        		icon: 'warning',
        		showCancelButton: false,
        		confirmButtonColor: '#3085d6',
        		cancelButtonColor: '#d33',
        		showConfirmButton: true,
        	    timer: "2000"
        	});
         return false; 
     }  
	
});

$(document).on('click', '.active_inactive_toggle', function(e){
        e.preventDefault();
       var  current_status = ($(this).val()==1)?'Suspend':'Activate';
       var role_title = $(this).attr('role_title');
        Swal.fire({
    		text: "Are you sure, you want to "+ current_status +" this " + role_title + " account?",
    		icon: 'warning',
    		showCancelButton: true,
    		confirmButtonColor: '#3085d6',
    		cancelButtonColor: '#d33',
    		confirmButtonText: 'Yes, '+current_status+'!'
    	}).then((result) => {
    		if (result.isConfirmed) {
    			var toggle_input = $(this);
    	        var user_id = toggle_input.attr('user_id');
	    	        $.ajax({
	    	           type:'POST',
	    	           url:config.routes.activateOrSuspendUser,
	    	           data:{user_id:user_id},
	    	           success:function(data){
	    	        	   toggle_input.val(data.user_status);
	    	        	   toggle_input.prop('checked',(data.user_status == 1) ? true : false);
	    	        	   toggle_input.next().attr('title',(data.user_status == 1) ? 'Active' : 'Suspend');
	    	        	   $("#toggle_text_"+user_id).text((data.user_status == 1) ? 'Active' : 'Suspend');
	    	        	   $("#account_filter_"+user_id).text(data.user_status);

	    	        	   Swal.fire({
	    	        		      icon: data.icon,
	    	        		      text: data.message,
	    	        		      showConfirmButton: true,
	    	        		      timer: 3000,
	    	        			});

	    	        	 
	    	           }
	    	        });    		
    	        }
    	});
        
});

$(document).on('click', '.active_inactive_toggle_1', function(e){
        e.preventDefault();
       var  current_status = ($(this).val()==1)?'Suspend':'Activate';
		var messge = messge;
		if($(this).val()==1){
		  var messge ="Are you sure, you want to suspended this account for three days?";
		}else{
			var messge ="Are you sure want  Suspend to Activate this account?";
		}
      
        Swal.fire({
    		text: messge,
    		icon: 'warning',
    		showCancelButton: true,
    		confirmButtonColor: '#3085d6',
    		cancelButtonColor: '#d33',
    		confirmButtonText: 'Yes, '+current_status+'!'
    	}).then((result) => {
    		if (result.isConfirmed) {
    			var toggle_input = $(this);
    	        var user_id = toggle_input.attr('user_id');
	    	        $.ajax({
	    	           type:'POST',
	    	           url:config.routes.activateOrSuspendUsers,
	    	           data:{user_id:user_id},
	    	           success:function(data){
	    	        	   toggle_input.val(data.user_status);
	    	        	   toggle_input.prop('checked',(data.user_status == 1) ? true : false);
	    	        	   toggle_input.next().attr('title',(data.user_status == 1) ? 'Active' : 'Suspend');
	    	        	   $("#toggle_text_"+user_id).text((data.user_status == 1) ? 'Active' : 'Suspend');
	    	        	   $("#account_filter_"+user_id).text(data.user_status);

	    	        	   Swal.fire({
	    	        		      icon: data.icon,
	    	        		      text: data.message,
	    	        		      showConfirmButton: true,
	    	        		      timer: 3000,
	    	        			});

	    	        	 
	    	           }
	    	        });    		
    	        }
    	});
        
});


$(document).on('click', '.approve_account_btn', function(e){
    e.preventDefault();
    Swal.fire({
		text: "Are you sure, you want to approve this Service Provider account?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes,approve!'
	}).then((result) => {
		if (result.isConfirmed) {
			var toggle_input = $(this);
	        var user_id = toggle_input.attr('user_id');
    	        $.ajax({
    	           type:'POST',
    	           url:config.routes.approveAccount,
    	           data:{user_id:user_id},
    	           success:function(data){
    	        	   location.reload(true);
    	        	   Swal.fire({
    	        		      icon: data.icon,
    	        		      text: data.message,
    	        		      showConfirmButton: true,
    	        		      timer: 3000,
    	        			});

    	        	 
    	           }
    	        });    		
	        }
	});
    
});

        
        
        $(document).on('click', '.reject_account_btn', function(e){
          
            Swal.fire({
        		  text: "Are you sure, you want to reject this Service Provider account?",
            	  input: 'textarea',
            	  inputAttributes: {
            	    autocapitalize: 'off'
            	  },
            	  showCancelButton: true,
            	  confirmButtonText: 'Yes,Reject!',
            	  showLoaderOnConfirm: true,
            	  preConfirm: (profile_reject_comment) => {
            	    return fetch(config.routes.rejectAccount)
            	      .then(response => {
            	    	  if(profile_reject_comment){
            	    		  		
            	    	        var user_id = $(this).attr('user_id');
            		    	        $.ajax({
            		    	           type:'POST',
            		    	           url:config.routes.rejectAccount,
            		    	           data:{user_id:user_id,profile_reject_comment:profile_reject_comment},
            		    	           success:function(data){
            		    	        	  
            		    	        	   //setTimeout(function () {
            		    	        		   location.reload(true);
            		    	        		   Swal.fire({
             		    	        		      icon: data.icon,
             		    	        		      text: data.message,
             		    	        		      showConfirmButton: true,
             		    	        		      timer: 3000,
             		    	        			});
            		    	        	     // }, 1000);
            		    	        	  

            		    	        	 
            		    	           }
            		    	        });    		
            	    	        
            	    	  }else{
            	    		  Swal.showValidationMessage(
                        	          'Please enter Enter profile reject comment.'
                        	        ) 
            	    	  }
            	       
            	      })
            	      .catch(error => {
            	        Swal.showValidationMessage(
            	          `Request failed: ${error}`
            	        )
            	      })
            	  },
            	  allowOutsideClick: () => !Swal.isLoading()
            	});
        
        
        });

        


    	
        $("#sub_cat_img").change(function(){
      	  var filePath = $(this).val();
      	 let allowedExtensions = /(\.jpg|\.jpeg|\.png|\.jpeg|\.gif)$/i; 
      	 if (!allowedExtensions.exec(filePath)) { 
               $(this).val(''); 
               Swal.fire({
              		text: "Invalid file type",
              		icon: 'warning',
              		showCancelButton: false,
              		confirmButtonColor: '#3085d6',
              		cancelButtonColor: '#d33',
              		showConfirmButton: true,
              	    timer: "2000"
              	});
               $("#sub_cat_img_error").show();
               $(".show_image_preview").attr('src',$(".show_image_preview").attr('default_url'));
               return false; 
           }
      	 if($(this).closest("form").length){
      		 $("#sub_cat_img_error").hide();
      		 $(this).closest("form").valid();
      	 }
          readURL(this);
      }); 



function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
        	$(".show_image_preview").attr('src',e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}



});

/* End Document Ready Here*/



/**
 * modal alert on user logout
 */
function confirmLogout() {
	Swal.fire({
		text: "Are you sure you want to logout?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, logout !'
	}).then((result) => {
		if (result.isConfirmed) {
			$("#logout_form").submit();
		}
	})
}


$(document).on('click', '.change_profile_status', function(e){
	Swal.fire({
	      title: $(this).attr('message'),
	      icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes!'
	  })
	  .then((result) => {
		var user_id = $(this).attr('user_id');
		var attribute = $(this).attr('attribute');
		var status = $(this).attr('status');
	      if (result.isConfirmed) {
	          $.ajax({
	              type: "POST",
	              url:config.routes.changeProfileStatus,
   	              data:{user_id:user_id,attribute:attribute,status:status},

	              success: function (data) {
	            	  console.log(data);
	            	// return false;
	                  if(data.status == true){
	                	  Swal.fire({
  	        		      icon: 'success',
  	        		      text: data.message,
  	        		      showConfirmButton: true,
  	        		      timer: 3000,
  	        			});
	                	  
	                      window.setTimeout(function(){location.reload()},3000)


	                    
	                  }else{
	                	  Swal.fire({
	  	        		      icon: 'error',
	  	        		      text: data.message,
	  	        		      showConfirmButton: true,
	  	        		      timer: 3000,
	  	        			}); 
	                      window.setTimeout(function(){location.reload()},3000)

	                  }

	                 
	                
	              },
	              error: function (data) {
	            	  Swal.fire({
	        		      icon: 'error',
	        		      text: data.message,
	        		      showConfirmButton: true,
	        		      timer: 3000,
	        			});
	                  window.setTimeout(function(){location.reload()},2000)

	              }
	          });
	      }
	      return false;
	  });

});

/*$(document).on('click', '.change_work_profile_status', function(e){

	sweetAlert();
}*/
function sweetAlertReject(data){
	  (async () => {
		  var message = $(data).attr('message');
		  var status = $(data).attr('status');
	  const { value: formValues } = await Swal.fire({
		    title: message,
		    icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes!',
	    html:
	      '<input id="swal-input1" class="swal2-input" placeholder="Reject Title" required>' +
	      '<input id="swal-input2" class="swal2-input" rows="4" placeholder="Reject description">',
	    focusConfirm: false,
	    preConfirm: () => {
	    	var user_id = $(data).attr('user_id');
			var attribute = $(data).attr('attribute');
			var status = $(data).attr('status');
	    	var input1 = document.getElementById('swal-input1').value;
	    	var input2 = document.getElementById('swal-input2').value;
	    	if(input1==''){
	    		
	    		Swal.showValidationMessage(
	    				'Please enter Enter profile reject comment title.'
	    		); 
	    		return false;
	    	}	
	    	if(input2==''){
	    		
	    		 Swal.showValidationMessage(
	         	          'Please enter Enter profile reject comment description.'
	         	        );  
	    		 return false;
	    	}	
	    	  $.ajax({
	              type: "POST",
	              url:config.routes.changeWorkProfileStatus,
   	              data:{user_id:user_id,attribute:attribute,status:status,reject_title:input1,reject_description:input2},

	              success: function (data) {
	            	
	                  if(data.status == true){
	                	  Swal.fire({
  	        		      icon: 'success',
  	        		      text: data.message,
  	        		      showConfirmButton: true,
  	        		      timer: 3000,
  	        			});
	                	  
	                      window.setTimeout(function(){location.reload()},3000)


	                    
	                  }else{
	                	  Swal.fire({
	  	        		      icon: 'error',
	  	        		      text: data.message,
	  	        		      showConfirmButton: true,
	  	        		      timer: 3000,
	  	        			}); 
	                      window.setTimeout(function(){location.reload()},3000)

	                  }

	                 
	                
	              },
	              error: function (data) {
	            	  Swal.fire({
	        		      icon: 'error',
	        		      text: data.message,
	        		      showConfirmButton: true,
	        		      timer: 3000,
	        			});
	                  window.setTimeout(function(){location.reload()},2000)

	              }
	          });
      	       
	    /*  return [
	        document.getElementById('swal-input1').value,
	        document.getElementById('swal-input2').value
	      ]*/
	    }
	  })
/*
	  if (formValues) {
	    Swal.fire(JSON.stringify(formValues))
	  }*/

	  })()
	}

function sweetAlert(data){
	  (async () => {
		  var message = $(data).attr('message');
		  var status = $(data).attr('status');
		 
	  const { value: formValues } = await Swal.fire({
		  title: message,
		    icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes!',
	    html:(status ==2)?
	      '<input id="swal-input1" class="swal2-input" placeholder="Reject Title" required>' +
	      '<input id="swal-input2" class="swal2-input" rows="4" placeholder="Reject description">':false,
	    focusConfirm: false,
		    preConfirm: () => {
	    	var user_id = $(data).attr('user_id');
			var attribute = $(data).attr('attribute');
			var status = $(data).attr('status');
			var input1 = input2 = null;
	    	if(status ==2){
	    		var input1 = document.getElementById('swal-input1').value;
	    		var input2 = document.getElementById('swal-input2').value;
	    		if(input1==''){
		    		
		    		Swal.showValidationMessage(
		    				'Please enter Enter work profile reject comment title.'
		    		); 
		    		return false;
		    	}	
		    	if(input2==''){
		    		
		    		 Swal.showValidationMessage(
		         	          'Please enter Enter work profile reject comment description.'
		         	        );  
		    		 return false;
		    	}	
	    	}
	    	
	    	
	    	  $.ajax({
	              type: "POST",
	              url:config.routes.changeWorkProfileStatus,
 	              data:{user_id:user_id,attribute:attribute,status:status,reject_title:input1,reject_description:input2},

	              success: function (data) {
	            	
	                  if(data.status == true){
	                	  Swal.fire({
	        		      icon: 'success',
	        		      text: data.message,
	        		      showConfirmButton: true,
	        		      timer: 3000,
	        			});
	                	  
	                      window.setTimeout(function(){location.reload()},3000)


	                    
	                  }else{
	                	  Swal.fire({
	  	        		      icon: 'error',
	  	        		      text: data.message,
	  	        		      showConfirmButton: true,
	  	        		      timer: 3000,
	  	        			}); 
	                      window.setTimeout(function(){location.reload()},3000)

	                  }

	                 
	                
	              },
	              error: function (data) {
	            	  Swal.fire({
	        		      icon: 'error',
	        		      text: data.message,
	        		      showConfirmButton: true,
	        		      timer: 3000,
	        			});
	                  window.setTimeout(function(){location.reload()},2000)

	              }
	          });
    	       
	   /*   return [
	        document.getElementById('swal-input1').value,
	        document.getElementById('swal-input2').value
	      ]*/
	    }
	  })

	/*  if (formValues) {
	    Swal.fire(JSON.stringify(formValues))
	  }*/

	  })()
	}


function deleteDataTableRecord(url, tableId){
	
	Swal.fire({
	      title: "Are you sure want to delete this record?",
	      icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, Delete !'
	  })
	  .then((result) => {
		
	      if (result.isConfirmed) {
	          $.ajax({
	              type: "DELETE",
	              url: url,
	              success: function (data) {
	            	  console.log(data.statusCode);
	                  if(data.statusCode == 200){
	                	  Swal.fire({
    	        		      icon: 'success',
    	        		      text: data.message,
    	        		      showConfirmButton: true,
    	        		      timer: 3000,
    	        			});

	                      let oTable = $('#'+tableId).dataTable(); 
	                      oTable.fnDraw(false);
	                  }

	                  if(data.statusCode == 404){
	                	  Swal.fire({
    	        		      icon: 'error',
    	        		      text: data.message,
    	        		      showConfirmButton: true,
    	        		      timer: 3000,
    	        			});

	                  }

	                  if(data.statusCode >= 500){
	                	  location.reload(true);
   	        		   Swal.fire({
    	        		      icon: 'error',
    	        		      text: data.message,
    	        		      showConfirmButton: true,
    	        		      timer: 3000,
    	        			});
	                  }
	              },
	              error: function (data) {
	            	  Swal.fire({
	        		      icon: 'error',
	        		      text: data.message,
	        		      showConfirmButton: true,
	        		      timer: 3000,
	        			});
	              }
	          });
	      }
	      return false;
	  });
	}


$(document).on('click', '.reject_common', function(e){

	  (async () => {
		  var message = $(this).attr('message');
		  var status = $(this).attr('status');
		
	  const { value: formValues } = await Swal.fire({
		    title: message,
		    icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes!',
	    html:
	      '<input id="swal-input1" class="swal2-input" placeholder="Reject Title" required>' +
	      '<input id="swal-input2" class="swal2-input" rows="4" placeholder="Reject description">',
	    focusConfirm: false,
	    preConfirm: () => {
	    	var user_id = $(this).attr('user_id');
			var attribute = $(this).attr('attribute');
			var attribute1 = $(this).attr('attribute1');
			var attribute2 = $(this).attr('attribute2');
			var status = $(this).attr('status');
	    	var input1 = document.getElementById('swal-input1').value;
	    	var input2 = document.getElementById('swal-input2').value;
	    	var profile_step = $(this).attr('profile_step');
	    	
	    	if(input1==''){
	    		
	    		Swal.showValidationMessage(
	    				'Please enter Enter profile reject comment title.'
	    		); 
	    		return false;
	    	}	
	    	if(input2==''){
	    		
	    		 Swal.showValidationMessage(
	         	          'Please enter Enter profile reject comment description.'
	         	        );  
	    		 return false;
	    	}	
	    	  $.ajax({
	              type: "POST",
	              url:config.routes.rejectCommon,
 	              data:{user_id:user_id,profile_step:profile_step,attribute:attribute,attribute1:attribute1,attribute2:attribute2,status:status,reject_title:input1,reject_description:input2},

	              success: function (data) {
	                  if(data.status == true){
	                	  Swal.fire({
	        		      icon: 'success',
	        		      text: data.message,
	        		      showConfirmButton: true,
	        		      timer: 3000,
	        			});
	                	  
	                      window.setTimeout(function(){location.reload()},3000)


	                    
	                  }else{
	                	  Swal.fire({
	  	        		      icon: 'error',
	  	        		      text: data.message,
	  	        		      showConfirmButton: true,
	  	        		      timer: 3000,
	  	        			}); 
	                      window.setTimeout(function(){location.reload()},3000)

	                  }

	                 
	                
	              },
	              error: function (data) {
	            	  Swal.fire({
	        		      icon: 'error',
	        		      text: data.message,
	        		      showConfirmButton: true,
	        		      timer: 3000,
	        			});
	                  window.setTimeout(function(){location.reload()},2000)

	              }
	          });
    	       
	    /*  return [
	        document.getElementById('swal-input1').value,
	        document.getElementById('swal-input2').value
	      ]*/
	    }
	  })
/*
	  if (formValues) {
	    Swal.fire(JSON.stringify(formValues))
	  }*/

	  })()
	
});