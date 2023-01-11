<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <title>Admin| Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('admin/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{asset('admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('admin/dist/css/adminlte.min.css')}}">
	  <link rel="stylesheet" href="{{asset('frontend/css/custom-style.css')}}">
	
</head>
<body class="hold-transition login-page">

<div class="login-box">
  <div class="login-logo">
    <a href="javascript:;"><b>Reset </b> Password</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Reset Password</p>

  	<form method="post" id="forgot_password" action="{{ route('newpassword') }}">
						@csrf
						<input type="hidden" name="user_id" value="{{ $data['id'] }}">
						<input type="hidden" name="token_id" value="{{ $data['token'] }}">
						
		
        <div class="input-group mb-3">
        <input type="password" name="new_password" placeholder="Enter new password" class="form-control" value="{{old('new_password')}}" id="password">

          <div class="input-group-append">
            <div class="input-group-text">
               <span class="fas fa-lock"></span>
            </div>
          </div>
          
          
        </div>
                         <span class="error">
                        		@if($errors->has('new_password'))
                            		{{ $errors->first('new_password') }}
                            	@endif
                             </span> 
        <div class="input-group mb-3">
         <input type="password" name="confrim_password" placeholder="Enter confirm Password" class="form-control" value="{{old('confrim_password')}}">

          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
           <span class="error">
                        		@if($errors->has('confrim_password'))
                            		{{ $errors->first('confrim_password') }}
                            	@endif
                             </span> 
        </div>
        <div class="row">
       
          <!-- /.col -->
          <div class="col-md-12 ">
            <button type="submit" class="btn btn-primary btn-block text-center">Submit </button>
          </div>
          
          <!-- /.col -->
        </div>
      </form>

    
    
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{asset('admin/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('admin/dist/js/adminlte.min.js')}}"></script>
<script type="text/javascript" src="{!! asset('frontend/js/jquery.validate.js')!!}"></script>
<script type="text/javascript" src="{!! asset('frontend/js/form-validate.js')!!}"></script>

</body>
</html>