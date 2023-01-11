<html>
<head> 
</head>
	<body>
		<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
  <div style="margin:50px auto;width:70%;padding:20px 0">
    <div style="border-bottom:1px solid #eee">
      <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">{{env('APP_NAME')}}</a>
    </div>
    <p style="font-size:1.1em">Hi,</p>
     <p style="font-size:1.1em"> {{$full_name}}</p>
   	<p style="text-align: left; margin: 10px 50px;  font-size: 15px">{{env('APP_NAME')}} admin update your account password .</p> 
   
    <p style="text-align: left; margin: 10px 50px;  font-size: 14px"> Your login details :</p>
        <p style="text-align: left; margin: 5px 50px;  font-size: 12px"> Email : {{$email}}</p>
        <p style="text-align: left; margin: 5px 50px;  font-size: 12px"> Password : {{$password}}</p>
    
     
    <p style="font-size:0.9em;">Regards,<br /> {{env('APP_NAME')}}</p>
    <hr style="border:none;border-top:1px solid #eee" />
    <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
<!--       <p>Your Brand Inc</p> -->
<!--       <p>1600 Amphitheatre Parkway</p> -->
<!--       <p>California</p> -->
<!--     </div> -->
  </div>
</div>
</div>
	</body>
	
</html>