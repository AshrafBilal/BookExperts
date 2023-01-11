

<html>
<head> 
</head>
	<body>
		<div style="text-align:left; width:100% !important;margin:0px auto;">
			
			<div style="border: 1px solid #d4cdcd;box-shadow: 1px 3px 12px 1px;">
				<h1 style="margin:20px">{{env('APP_NAME', "Just Say What") }}</h1>
				<h2 style="margin:20px"></h2>			 
					<h3 style="text-align: left; margin: 10px 50px;">Hi <b>
						 Super Admin		
					</b>
					</h3> 
					<p style="text-align: left; margin: 10px 50px;  font-size: 15px">It's look like {{$role}} <b>{{@$full_name}}</b> try to contact with you.</p> 
                    <p style="text-align: left; margin: 10px 50px;  font-size: 15px"><b>Contact Category:</b> {{@$contact_category}}</p> 
					<p  style="text-align: left; margin: 10px 50px;  font-size: 15px" ><b>{{$role}} Message:</b> {{@$description}}</p>

				 <br>
				 <br> 
	<!-- 			<p style="text-align: left; margin: 10px 50px;  font-size: 15px">
					This email has been sent to you have an Super Admin account on <b><i>{{env('APP_NAME', "Where To Pet Dispatch") }}</i></b> -->
<!-- 				</p> -->
				<br>
			</div>
			<div style="background: #17a2b8!important; padding: 20px; color:white;">
				
				
				<div>
					<h5>&#169; Copyright {{Date("Y")}} ,{{env('APP_NAME', "Just Say What") }} All Rights Reserved.</h5>

				</div>
			</div>
		</div> 
	</body>
</html>

