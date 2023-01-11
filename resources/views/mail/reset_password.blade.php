@php
$imgSrc = asset('admin/dist/img/AdminLTELogo.png');
@endphp

<html>
<head> 
</head>
	<body>
		<div style="text-align:left; width:100% !important;margin:0px auto;">
			<div style="background: #17a2b8!important; padding: 20px">
				<a href="{{ url('/') }}">
					<img alt="Where To Pet Dispatch logo" src="{{$imgSrc}}" width="100px" height="100px"/>
				</a>
			</div>
			<div style="border: 1px solid #d4cdcd;box-shadow: 1px 3px 12px 1px;">
				<h1 style="margin:20px">{{env('APP_NAME', "Where To Pet Dispatch") }}</h1>
				<h2 style="margin:20px"></h2>			 
					<h3 style="text-align: left; margin: 10px 50px;">Hi <b>
						{{$full_name}},		
					</b>
					</h3> 
					<p style="text-align: left; margin: 10px 50px;  font-size: 15px">It's look like your requested a new passowrd</p> 
					<p  style="text-align: left; margin: 10px 50px;  font-size: 15px" >If that sound right, you can enter new password by clicking on the button below.</p>

					<a style="text-decoration: none; float: left; margin: 0px 48px; background-color: #17a2b8!important;;  padding: 7px 25px; color: #fff; display: inline-block; font-size: 15px;" class="button" href="{{ route('forgot_password',['token' => $token, 'user_id'=> $id])}}">Reset Password</a>
				 <br>
				 <br> 
				<p style="text-align: left; margin: 10px 50px;  font-size: 15px">
					This email has been sent to you have an account on <b><i>{{env('APP_NAME', "Where To Pet Dispatch") }}</i></b>
				</p>
				<br>
			</div>
			<div style="background: #17a2b8!important; padding: 20px; color:white;">
				
				<div>
					<a href="{{ url('/') }}"><img src="{{$imgSrc}}" width="100px" height="100px"/></a>
				</div>
				<div>
					<h5>&#169; Copyright {{Date("Y")}} ,{{env('APP_NAME', "Where To Pet Dispatch") }} All Rights Reserved.</h5>

				</div>
			</div>
		</div> 
	</body>
</html>