@extends('layouts.admin-login')
@section('content')
   
    <!--begin::Main-->
        <div class="d-flex flex-column flex-root">
            <!--begin::Login-->
            <div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid " id="kt_login" style=" background-image:url({{asset('admin-login/img/jsw_admin.png')}});background-size: cover; background-repeat: no-repeat;">
                
<!--begin::Aside-->
<div class="login-aside d-flex flex-column flex-row-auto">
    <!--begin::Aside Top-->
    <div class="d-flex flex-column-auto flex-column pt-lg-40 pl-40 ">
        <!--begin::Aside header-->
        <a href="{{url('/')}}" class="text-center mb-10 mt-40 logo_inn">
          	<div class="text-left pl-10">
			<h3 class="wlcm-back">Welcome Back!</h3>		
			<p class="wlcm-sigin">Sign in to your account</p>
			</div>
        </a> 
	
		
    </div>
    <!--end::Aside Top-->
    <!--begin::Aside Bottom-->

    <!--end::Aside Bottom-->
</div>
<!--begin::Aside-->
<!--begin::Content-->
<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden  mx-auto ">
    <!--begin::Content body-->
    <div class="d-flex flex-column-fluid flex-center p-7" >
        <!--begin::Signin-->
        <div class="login-form login-signin bg-white pb-20 pt-10 pl-15 pr-15 rounded-lg" >
            <!--begin::Form-->
             <form  action="{{ route('admin.login') }}" method="post" id="admin_login_form" class="form">
        		@method('post')
        		@csrf
                <!--begin::Title-->
                              <div class="pb-10 pt-lg-7 pt-10">
                    <!-- <h3 class="font-weight-bolder  text-center admin_hding_clr">Admin Login</h3> -->
                    <div class="text-center login_heading_text"><img src="{{asset('admin-login/img/logo.png')}}" alt=""></div>
                </div>
                <!--begin::Title-->
                <!--begin::Form group-->
                <div class="form-group img_set">
                   <!--  <label class="font-size-h6 font-weight-bolder text-dark">Email</label> -->
					 <input class="form-control form-control-solid h-auto py-5 px-5 rounded-sm" autocomplete="email" autofocus type="email" name="email"  placeholder="Email" value="{{old('email')}}" id="login_email_input">
                   
                   		 <span class="error">
                        		@if($errors->has('email'))
                            		{{ $errors->first('email') }}
                            	@endif
                             </span> 
				</div>
                <!--end::Form group-->
                <!--begin::Form group-->
                <div class="form-group img_set">
                    <!-- <div class="d-flex justify-content-between mt-n5">
                        <label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
                        
                    </div> -->
					 <input class="form-control form-control-solid h-auto py-5 px-5   rounded-sm " autocomplete="current-password" type="password" name="password" placeholder="Password"  value="{{old('password')}}">
                    <span class="error">
                        		@if($errors->has('password'))
                            		{{ $errors->first('password') }}
                            	@endif
                             </span> 
				</div>
                <!--end::Form group-->
                <!--begin::Action-->
                <div class="pb-lg-0 pb-5  mt-15 text-center">
                   <button class="btn btn_size font-weight-bold btn-bg-primary text-white btn-block font-size-h3 px-8 py-4 my-0 mr-0 mb-0" type="submit" name="submit" value="Sign In">Login</button>
                </div>
                <!--end::Action-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Signin-->
    </div>
   
</div>
<!--end::Content-->
                     
            </div>
            <!--end::Login-->
        </div>
        <!--end::Main-->

      
@endsection