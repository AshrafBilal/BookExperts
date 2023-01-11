@extends('layouts.admin')
@section('content')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Update Service Category</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('serviceCategory.index')}}">Service Categories</a></li>
              <li class="breadcrumb-item active"> Update Service Category</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
       <form method="post" ACTION="{{ route('serviceCategory.update', base64_encode($service->id)) }}"  id="add_service_category_form" class="common_form" enctype="multipart/form-data">
				 @method('PUT')
				 @csrf
        <div class="col-md-6">
          <div class="card card-primary">
         
            <div class="card-body">
              <div class="form-group">
                <label for="inputName">Category Name</label>
                <input type="text" id="inputName" class="form-control" name="name" value="{{!empty(old('first_name'))?old('first_name'):@$service->name}}">
                 <span class="error">
            		@if($errors->has('name'))
                		{{ $errors->first('name') }}
                	@endif
                 </span> 
              </div>
                 
            </div>
            <!-- /.card-body -->
          </div>
        <div class="col-12  text-center">
          <input type="submit" value="Save" class="btn btn-success float-right">
        </div>
          <!-- /.card -->
        </div>
        
    
		</form>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 
  
  @endsection
  
   @php
 
   if(!empty(old('country_code'))){
   $countryCode = str_replace("+", "", old('country_code'));
   $countryCode =  !empty(old('country_code'))?getCountryCode($countryCode):"us";
   }else{
   $countryCode = "us";
   }
  @endphp
  
  @section('scripts')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/css/intlTelInput.css" rel="stylesheet" media="screen">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/intlTelInput.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/intlTelInput.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"></script>
 <script type="text/javascript">
 	$("#contact_number").intlTelInput({
		  utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/js/utils.js",
		  formatOnDisplay:false,
		});
	$("#contact_number").intlTelInput("setCountry", "{{$countryCode}}");

	$('.country').click(function () {
		var code = $(this).children("span.dial-code").text();
		if(code){
			$("#country_code").val(code);
			}
	   
	});
	
  </script>
 
  @endsection