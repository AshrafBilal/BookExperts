<!doctype html>
<html lang="en">
   <head>
      <title>
      Admin Login
      </title> 
	 <meta charset="utf-8">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{!! asset('frontend/images/favicon.ico')!!}" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link rel="stylesheet" href="{{asset('admin/css/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{!! asset('frontend/css/style.bundle.css')!!}" />
    <link rel="stylesheet" href="{!! asset('frontend/css/login-1.css')!!}" />
            
    </head> 

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading"> 

 @include('layouts.flash-message')
 @yield('content')




    <!-- jQuery -->
    <script src="{{asset('admin/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script type="text/javascript" src="{!! asset('frontend/js/jquery.validate.js')!!}"></script>
    <script type="text/javascript" src="{!! asset('frontend/js/form-validate.js')!!}"></script>
    <script src="{{asset('admin/js/sweetalert2.min.js')}}"></script>
    
 @yield('after_footer')

 
 </body>
 
</html> 

