@php
use Illuminate\Support\Facades\Auth;
$user = Auth::user();
@endphp
<!doctype html>
<html lang="en">
   <head>
      <title>
        @if (trim($__env->yieldContent('title'))) 
   		 @yield('title') 
      	@else
      	{{setpageTitle()}}
      	@endif
      </title> 
	 <meta charset="utf-8">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
          <link rel="shortcut icon" href="{!! asset('frontend/images/favicon.ico')!!}" type="image/x-icon">
      
	    <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
   <link rel="stylesheet" href="{{asset('admin/plugins/fontawesome-free/css/all.min.css')}}">
   <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset('admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
<!--   <link rel="stylesheet" href="{{asset('admin/plugins/jqvmap/jqvmap.min.css')}}"> -->
  <!-- Theme style -->
   <link rel="stylesheet" href="{{asset('admin/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('admin/plugins/summernote/summernote-bs4.min.css')}}">
  <link rel="stylesheet" href="{{asset('admin/css/custom-style.css')}}">
  <link rel="stylesheet" href="{{asset('admin/css/sweetalert2.min.css')}}">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="{!! asset('admin/css/full-calender-main.css')!!}">
  <link rel="stylesheet" href="{!! asset('admin/css/jquery.ccpicker.css')!!}">
      <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/css/bootstrap-toggle.css" integrity="sha512-9tISBnhZjiw7MV4a1gbemtB9tmPcoJ7ahj8QWIc0daBCdvlKjEA48oLlo6zALYm3037tPYYulT0YQyJIJJoyMQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />	
     
 
   
    
   </head>
<body>

<div class="wrapper">


  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{asset('admin/dist/img/AdminLTELogo.png')}}" alt="AdminLTELogo" height="60" width="60">
  </div>



<!--end header area here -->
 @include('includes.admin-topbar')

 @include('includes.admin-sidebar')
 @include('layouts.flash-message')
 @yield('content')
 @include('includes.admin-footer')
 @include('includes.ajax-url')

</div>
<!-- jQuery -->
<script src="{{asset('admin/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
 <script src="{{asset('admin/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
 <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<!-- Sparkline -->

<!-- JQVMap -->
<!-- <script src="{{asset('admin/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{asset('admin/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script> -->
<!-- jQuery Knob Chart -->
<script src="{{asset('admin/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{asset('admin/plugins/moment/moment.min.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
 <script src="{{asset('admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
 <!-- Summernote -->
 <script src="{{asset('admin/plugins/summernote/summernote-bs4.min.js')}}"></script>
 <!-- overlayScrollbars -->
<!-- <script src="{{asset('admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script> -->
<!-- AdminLTE App -->
<script src="{{asset('admin/dist/js/adminlte.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('admin/dist/js/demo.js')}}"></script>
<script type="text/javascript" src="{!! asset('admin/js/ckeditor.js')!!}"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('admin/dist/js/pages/dashboard.js')}}"></script>
<script src="{{asset('admin/js/sweetalert2.min.js')}}"></script>
<script type="text/javascript" src="{!! asset('frontend/js/jquery.validate.js')!!}"></script>
<script src="{{asset('admin/js/additional-methods.min.js')}}"></script>
<script src="{{asset('admin/js/admin-scripts.js')}}"></script>
<script src="{{asset('admin/js/admin-form-validation.js')}}"></script>
<script type="text/javascript" src="{!! asset('admin/js/full-calender-main.js')!!}"></script>
<script type="text/javascript" src="{!! asset('admin/js/jquery.ccpicker.min.js')!!}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js" integrity="sha512-o0rWIsZigOfRAgBxl4puyd0t6YKzeAw9em/29Ag7lhCQfaaua/mDwnpE2PVzwqJ08N7/wqrgdjc2E0mwdSY2Tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js" integrity="sha512-F636MAkMAhtTplahL9F6KmTfxTmYcAcjcCkyu0f0voT3N/6vzAuJ4Num55a0gEJ+hRLHhdz3vDvZpf6kqgEa5w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{asset('admin/js/owl.carousel.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>

@php
 $key = setting('googe_api_key');
 $key = ! empty($key) ? $key : 'AIzaSyB8L96ED27rCDdxOF3DNAagrQzEi3jRUAE';
@endphp
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={{$key}}&libraries=places"></script>
    <script>

    $(function(){ // this will be called when the DOM is ready
    	  $('.searchInput').keyup(function() {
    		  $("#latitude").val('');
              $("#longitude").val('');
    	  });

    	  $('.searchInput').bind('copy paste cut',function(e) {
    		  e.preventDefault();
    		});
    	});
    	    
        $("form input").keypress(function (e) {
            if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
                $(this).closest('form').submit();
            } else {
                return true;
            }
        });
    $(function() {
     $(".autocomplete").keypress(function (e) {
      
            var options = {
               // types: ['address'],
            };
            var autocomplete = new google.maps.places.Autocomplete($(this)[0], options);
            //var autocomplete = new google.maps.places.Autocomplete(input,{types: ['(cities)']});
            google.maps.event.addListener(autocomplete, 'place_changed', function(){
                var place = autocomplete.getPlace();
                var lat =  place.geometry.location.lat();
                var lng =  place.geometry.location.lng();
                var city = place.address_components[1].long_name;
                var state = place.address_components[2].long_name;
                //var country = place.address_components[3].long_name;
                $("#latitude").val(lat);
                $("#longitude").val(lng);
    
            });
     
   		 });
    
     }); 
        
    </script>

 @yield('scripts')

  @yield('after_footer')

</body>
</html>