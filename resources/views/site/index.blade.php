@extends('layouts.guest')
@section('content')
@section('title') Home @endsection


@push('page_css')
<link rel="stylesheet" href="{{asset('frontend/css/owl.carousel.min.css')}}">
<link rel="stylesheet" href="{{asset('frontend/css/owl.theme.min.css')}}">

@endpush


<!-- banner-section start-->
 <section class="pt-10">
	 <div class="container">
	 <div class="banner-section aos-item pb-35" data-aos="fade-up">
		 <div class="row">
			<div class="col-sm-6">
				<div class="banner_left pl-10 pt-15 ">
					<h2 class="pt-sm-30 pb-3">Whatever you want. Whenever you want.
					<span> <img src="{{asset('frontend/images/Justsaywhat.png')}}" alt=""></span></h2>
					<p class="">Schedule your services with individuals and businesses for any type of service you require.</p>
					<div class="banner_left-link pt-15"><a href="#">Download Our App</a></div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="banner-img">
					<!--  <img src="{{asset('frontend/images/banner-img.png')}}" alt="">-->
				</div>
			</div>
		 </div>
		 </div>
	 </div>
 </section>
<!-- banner-section end-->
<!-- about section start  -->
	<section class="py-20 aos-item" data-aos="fade-up">
	<div class="container">
		<div class="heading_title text-center pt-3"><h3>About <span>Just say what</span></h3></div>
		<div class="about-text py-7 text-center px-sm-30">
			<p>
			The world believes our lives will be based on booking in 5 years time, we decided to create a booking platform for all handy service providers, inclusive of a social media feature where they can promote their services by sharing images and videos. With only one click, you may book your preferred service provider. They're already waiting for you to visit or drop by at a moment's notice at a time that works best for you.
			</p>
		</div>
	</div>
	</section>
<!-- about-section end -->
 <section class="aos-item" data-aos="fade-up" id="counter-box">
	 <div class="container-fluid">
		 <div class="row">
		 <div class="col-sm-6 pl-0">
				<div class="banner-img pr-10">
					<img src="{{asset('frontend/images/find-job.png')}}" alt="">
				</div>
			</div>
			<div class="col-sm-6">
				<div class="find-jo pr-sm-20 pt-15  ">
					<h2 class=" pb-3 pr-30">Find a job you love</h2>
					<!-- <p class="photographs pr-5">
					Show the world you’re videos and photographs to become an <span>expert</span> in your profession, not just a specialist.
					</p> -->
					<p>Show the world your videos and photographs showcasing your <span>expertise</span> in your profession.</p>
					<div class=" find-job_cat">
				
				<div class="row border-buttom">
					<div class="col-sm-6 border-right ">
					<div class="job-find-cat py-10">
						<h4 class="timer" data-from="0" data-to="40" data-speed="1000" data-end="+" >40+</h4>
						<p>Job Category</p>
					</div>
					</div>
					<div class="col-sm-6">
					<div class="job-find-cat p-10">
						<h4 class="timer" data-from="0" data-to="120" data-speed="1000" data-end="+">120+</h4>
						<p>Jobs Available</p>
					</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6 border-right ">
					<div class="job-find-cat py-10">
						<h4 class="timer" data-from="0" data-to="100" data-speed="1000"data-end="%" >100%</h4>
						<p>Reliable Income</p>
					</div>
					</div>
					<div class="col-sm-6">
					<div class="job-find-cat p-10">
						<h4 class="timer" data-from="0" data-to="4543" data-speed="1000" data-end="">4543</h4>
						<p>Users Worldwide</p>
					</div>
					</div>
				</div>
				</div>
				<div class="banner_left-link pt-10 text-center"><a href="#">Download Our App</a></div>
			</div>
			</div>
			
		 </div>
	 </div>
 </section>
<!-- findjob-section end-->
<!-- about-section end -->
 <section class="how-it-work pb-40 mt-3 aos-item" data-aos="fade-up">
	 <div class="container">
	 <div class="heading_title text-center pt-20 pb-10"><h3>How  <span>It Works</span></h3></div>
		<div class="row">
			<div class="col-sm-5 pt-sm-20">
			<div class="pt-20 for-cutm pr-10 pl-sm-10">
				<h4 class="Poppins pb-3"> For Customer</h4>
				<p>All you have to do is download the app below, and follow the simple steps.</p>
				<div class="google_icon pt-15">
					<a href="{{setting('android_customer_app_link')}}"><img src="{{asset('frontend/images/google-app.svg')}}" alt=""></a>
					<a href="{{setting('ios_customer_app_link')}}"><img src="{{asset('frontend/images/apply-app.svg')}}" alt=""></a>
					
				</div>
			</div>
			</div>
			<div class="col-sm-7">
			<div class="row ">
					<div class="col-sm-6  ">
					<div class="it-work_right py-5">
						<img src="{{asset('frontend/images/it-work4.svg')}}" alt="">
						<h4 class="Poppins">Create Your Account</h4>
						<p>Provide name email<br> address, phone<br> number.</p>
					</div>
					</div>
					<div class="col-sm-6">
					<div class="it-work_right  it-work_right_col  py-5">
					<img src="{{asset('frontend/images/it-work1.svg')}}" alt="">
						<h4 class="Poppins">Select Service</h4>
						<p>Find out who the best service<br> provider in your area is or the<br> shop you want to visit.</p>
					</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6 ">
					<div class="it-work_right it-work_right_col2 py-5">
					<img src="{{asset('frontend/images/it-work2.svg')}}" alt="">
						<h4 class="Poppins">Select Availability</h4>
						<p>Schedule the most convenient time<br> for you.</p>
					</div>
					</div>
					<div class="col-sm-6">
					<div class="it-work_right it-work_right_col3 py-5">
					<img src="{{asset('frontend/images/it-work3.svg')}}" alt="">
							<h4 class="Poppins">Select Address & Done</h4>
						<p>Select your address to<br> get services to<br> your door steps</p>
					</div>
					
				</div>
			</div>
	</div>
	  </div>
	  </div>
 </section>
 <!-- service section -->
 <section class="service-section py-20 Poppins aos-item" data-aos="fade-up">
    <div class="container">
	<div class="row">
		<div class="col-sm-6">
			<div class="service_img"><img src="{{asset('frontend/images/service-img.png')}}" alt=""></div>
		</div>
		<div class="col-sm-6">
		<h2 class=" pb-3 Poppins Service-heading">For Service Provider</h2>
					<p class="pr-10 pb-10">Run your entire social media business account and booking system within our app and manage all your clients.</p>
		    <div class="timeline pr-sm-15">
                <div class="timeline-container primary">
                    <div class="timeline-icon">
                       <img src="{{asset('frontend/images/service1.svg')}}" alt="">
                    </div>
                    <div class="timeline-body">
                       
                        <p>Create account & complete your profile</p>
                       
                    </div>
                </div>
				 <div class="timeline-container success">
                    <div class="timeline-icon" style="top:13px;">
                        <img src="{{asset('frontend/images/service3.svg')}}" alt="">
                    </div>
                    <div class="timeline-body">
                       
                        <p>You can add media, like photos videos and story</p>
                       
                    </div>
                </div>
                <div class="timeline-container warning">
                    <div class="timeline-icon" style="top:13px;">
                       <img src="{{asset('frontend/images/service4.svg')}}" alt="">
                    </div>
                    <div class="timeline-body">
                       
                        <p>Meet customer you’re excited to work with & take your career or business to new heights.</p>
                       
                    </div>
                </div>
                <div class="timeline-container danger">
                    <div class="timeline-icon">
                        <img src="{{asset('frontend/images/service2.svg')}}" alt="">
                    </div>
                    <div class="timeline-body">
                       
                        <p>Set your availability and work accordingly</p>
                        
                    </div>
                </div>
               
                
               
            </div>
			<div class="google_icon Poppins text-center pl-sm-10">
			
					<a href="{{setting('android_provider_app_link')}}"><img src="{{asset('frontend/images/google-app.svg')}}" alt=""></a>
					<a href="{{setting('ios_provider_app_link')}}"><img src="{{asset('frontend/images/apply-app.svg')}}" alt=""></a>
					
		</div>
		</div>
	</div>
        
        </div>


 </section>
 <section class="testimonial_section">
  <div class="demo Poppins" id="testimonial-slider-inner">
    <div class="container">
 <div class="heading_title text-center pt-20 pb-15"><h3>What they’re   <span>Saying</span></h3></div>
        <div class="row"><div class=" col-md-12 ">
<div id="testimonial-slider" class="owl-carousel">
<div class="testimonial"><p class="description">Aristotle maintained the sharp distinction between science and the practical knowledge of artisans, treating theoretical speculation as the highest type of human </p>
                        <h3 class="title">williamson</h3>
                        <span class="post">Web Developer</span>
                    </div>
                    <div class="testimonial"><p class="description">Aristotle maintained the sharp distinction between science and the practical knowledge of artisans, treating theoretical speculation as the highest type of human  </p><h3 class="title">Kristina</h3><span class="post">Web Designer</span></div>
                    <div class="testimonial">
                        <p class="description">Aristotle maintained the sharp distinction between science and the practical knowledge of artisans, treating theoretical speculation as the highest type of human </p>
                        <h3 class="title">Miranda Joy</h3>
                        <span class="post">Web Developer</span>
                    </div>
<div class="testimonial"><p class="description">Aristotle maintained the sharp distinction between science and the practical knowledge of artisans, treating theoretical speculation as the highest type of human</p>
                        <h3 class="title">Miranda Joy</h3>
                        <span class="post">Web Developer</span>
                    </div>
<div class="testimonial"><p class="description">Aristotle maintained the sharp distinction between science and the practical knowledge of artisans, treating theoretical speculation as the highest type of human </p>
                        <h3 class="title">Miranda Joy</h3>
                        <span class="post">Web Developer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 </section>
 <!-- section app -->
 <section class="just-say-section pb-40 aos-item" data-aos="fade-up" >
	 <div class="container">
	 <div class="row">
		<div class="col-sm-7 pl-sm-20">
		<div class="heading_title heading_title-font_size pt-30 pb-10"><h3>There is more to love in the   <span>apps</span></h3></div>
		<p>Download our app from the store</p>
		<div class="google_icon pt-10 Poppins">
			<h4>CUSTOMER APP</h4>
					<a href="{{setting('android_customer_app_link')}}"><img src="{{asset('frontend/images/google-app.svg')}}" alt=""></a>
					<a href="{{setting('ios_customer_app_link')}}"><img src="{{asset('frontend/images/apply-app.svg')}}" alt=""></a>
					
		</div>
		<div class="google_icon pt-10 Poppins" id="elementtoScrollToID">
			<h4>PROVIDER APP</h4>
					<a href="{{setting('android_provider_app_link')}}"><img src="{{asset('frontend/images/google-app.svg')}}" alt=""></a>
					<a href="{{setting('ios_provider_app_link')}}"><img src="{{asset('frontend/images/apply-app.svg')}}" alt=""></a>
					
		</div>
		</div>
		</div>
	 </div>
 </section>
  <section class="contact_section pb-0 aos-item" data-aos="fade-up" >
	<div class="container" >
	<div class="contact_section_inner mb-30 mt-10">
	
	
	<div class="row" >
		<div class="col-sm-7 pl-sm-10">
			<div class="heading_title pb-15 pl-20 pt-20 text-white"><h3 class="text-white">Contact Us</h3></div>
			<div class="adrrss Poppins text-white pl-sm-40 position-relative pb-5"><span class="adrrss_icon" ><img src="{{asset('frontend/images/location.png')}}" alt=""></span>
			{!!setting('address')!!}
			</div>
			<div class="adrrss Poppins text-white pl-sm-40 position-relative pt-5 font-weight-normal"><span class="adrrss_icon" ><img src="{{asset('frontend/images/company.png')}}" alt=""></span>Company number: <span class="font-weight-bold">{{setting('company_number')}}</span>
</div>
		</div>
		<div class="col-sm-5">
		<div class="px-10 pt-10">
		<div class="bg-white pt-7 px-10 mb-10  rounded-sm">
		<div class="py-5 text-center"><img src="{{asset('frontend/images/mail-msg.png')}}" alt=""> 
		</div>
		<form method="POST" id="contact-us" action="{{route('contact-us')}}">
		@csrf
			<div class="form-group Poppins w-55 m-auto pb-5" >
				<div class="input_type_inner position-relative">
				
					<input class="form-control Poppins  form-control-solid font-size-h4 h-auto py-5 px-4 bg-white rounded-md  " placeholder="Your Name" type="text" name="name" value="{{ old('name') }}" required="">
				
				</div>
			</div>
			<div class="form-group Poppins w-55 m-auto pb-5">
				<div class="input_type_inner position-relative">
				
					<input class="form-control Poppins  form-control-solid font-size-h4 h-auto py-5 px-4 bg-white rounded-md  " placeholder="Your Email" type="email" name="email" value="{{ old('email') }}" required="">
				
				</div>
			</div>
			<div class="form-group Poppins w-55 m-auto pb-5">
				<div class="input_type_inner position-relative">
				
					<textarea class=" form-control popins_font  form-control-solid font-size-h4 h-auto py-5 px-4 bg-white rounded-md  " placeholder="Enter text here..." name="message" value="{{ old('message') }}" required=""></textarea>
				
				</div>
			</div>
			<div class="  m-auto btn-contect_form text-center  pb-7 ">
				
                    <button class="btn btn-dark popins_font border-0 btn-block font-weight-bold font-size-h3 px-10 py-4 my-0 mr-0 " type="submit" name="submit" value="Sign In">Send</button>
                </div>
			</form>
			</div>
				</div>
		</div>
	</div>
	</div>
	
		
	</div>
  </section>
	






@push('page_scripts')
<script src="{{asset('frontend/js/owl.carousel.min.js')}}"></script>



<script>

 
$(document).ready(function(){
    $("#testimonial-slider").owlCarousel({
        items:1,
        itemsDesktop:[1000,1],
        itemsDesktopSmall:[979,1],
        itemsTablet:[768,1],
        pagination:true,
        navigation:false,
        navigationText:["",""],
        slideSpeed:1000,
        autoPlay:true
    });
});

(function ($) {
	  $.fn.countTo = function (options) {
	    options = options || {};

	    return $(this).each(function () {
	      // set options for current element
	      var settings = $.extend(
	        {},
	        $.fn.countTo.defaults,
	        {
	          from: $(this).data("from"),
	          to: $(this).data("to"),
	          speed: $(this).data("speed"),
	          refreshInterval: $(this).data("refresh-interval"),
	          decimals: $(this).data("decimals")
	        },
	        options
	      );

	      // how many times to update the value, and how much to increment the value on each update
	      var loops = Math.ceil(settings.speed / settings.refreshInterval),
	        increment = (settings.to - settings.from) / loops;

	      // references & variables that will change with each update
	      var self = this,
	        $self = $(this),
	        loopCount = 0,
	        value = settings.from,
	        data = $self.data("countTo") || {};

	      $self.data("countTo", data);

	      // if an existing interval can be found, clear it first
	      if (data.interval) {
	        clearInterval(data.interval);
	      }
	      data.interval = setInterval(updateTimer, settings.refreshInterval);

	      // initialize the element with the starting value
	      render(value);

	      function updateTimer() {
	        value += increment;
	        loopCount++;

	        render(value);

	        if (typeof settings.onUpdate == "function") {
	          settings.onUpdate.call(self, value);
	        }

	        if (loopCount >= loops) {
	          // remove the interval
	          $self.removeData("countTo");
	          clearInterval(data.interval);
	          value = settings.to;

	          if (typeof settings.onComplete == "function") {
	            settings.onComplete.call(self, value);
	          }
	        }
	      }

	      function render(value) {
		      
	        var formattedValue = settings.formatter.call(self, value, settings);
	        var dataend = $(self).attr('data-end');
	             $self.text(formattedValue+dataend);
	      }
	    });
	  };

	  $.fn.countTo.defaults = {
	    from: 0, // the number the element should start at
	    to: 0, // the number the element should end at
	    speed: 1000, // how long it should take to count between the target numbers
	    refreshInterval: 100, // how often the element should be updated
	    decimals: 0, // the number of decimal places to show
	    formatter: formatter, // handler for formatting the value before rendering
	    onUpdate: null, // callback method for every time the element is updated
	    onComplete: null // callback method for when the element finishes updating
	  };

	  function formatter(value, settings) {
	    return value.toFixed(settings.decimals);
	  }
	})(jQuery);

	jQuery(function ($) {
	  // start all the timers
	  $(".timer").each(count);

	  // restart a timer when a button is clicked
	  $(window).scroll(function () {
	    console.log($(window).scrollTop());
	    if ($(window).scrollTop() > 100 && $(window).scrollTop() < 600) {
	      $(".timer").each(count);
	    }
	  });

	  function count(options) {
	    var $this = $(this);
	    options = $.extend({}, options || {}, $this.data("countToOptions") || {});
	    $this.countTo(options);
	  }
	});

	$("#contact_us_btn").click(function() {
	    $([document.documentElement, document.body]).animate({
	        scrollTop: $("#elementtoScrollToID").offset().top
	    }, 5000);
	});

</script>
@endpush
@endsection


