<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="shortcut icon" href="{!! asset('frontend/images/favicon.png')!!}" type="image/x-icon">

<link rel="stylesheet" href="{{asset('frontend/css/style.bundle.home.css')}}">
<link rel="stylesheet" href="{{asset('frontend/css/style.css')}}">
<link rel="stylesheet" href="{{asset('frontend/css/slider.css')}}">
<link rel="stylesheet" href="{{asset('frontend/css/timeline.css')}}">
  <link rel="stylesheet" href="{{asset('admin/css/sweetalert2.min.css')}}">
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<!-- <script src="js/bootstrap.min.js" ></script> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">



    @yield('third_party_stylesheets')

    @stack('page_css')
</head>

<body>

    <!-- start header -->
    <div class="header py-7 Poppins" id="myHeader">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                  <a  class="navbar-brand logo-img" href="{{route('/')}}" ><img src="{{asset('frontend/images/index-logo.png')}}" alt=""></a>
                  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                  </button>

                  <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                      <li class="nav-item {{request()->routeIs('/') ? 'active' : ''  }}">
                        <a class="nav-link" href="{{route('/')}}">Home</a>
                      </li>
                      <li class="nav-item {{request()->routeIs('term-condition') ? 'active' : ''  }}">
                        <a class="nav-link" href="{{route('term-condition')}}">Terms & Conditions</a>
                      </li>
                     
                      <li class="nav-item {{request()->routeIs('privacy-policy') ? 'active' : ''  }}">
                        <a class="nav-link " href="{{route('privacy-policy')}}">Privacy Policy</a>
                      </li>
                        <li class="nav-item {{request()->routeIs('aboutUs') ? 'active' : ''  }}">
                        <a class="nav-link " href="{{route('aboutUs')}}">About Us</a>
                      </li>
                    </ul>
                    @if(request()->routeIs('/'))
                    <a href="#contact-us" id="contact_us_btn" class="btn btn-outline-success my-2 my-sm-0 font-size-h4 font-weight-Medium ml-sm-10" type="submit">Contact Us</a>
                    @endif
                   
                  </div>
            </nav>
        </div>
    </div>
<!-- end the header -->




@include('layouts.flash-message')
@yield('content')


    <!-- footer -->
    <section class="footer pt-30 Poppins">
        <div class="container">
        <div class="row footer_mobile">
            <div class="col-sm-12 col-lg-3 w pr-10">
                <div class="cuntry">
                <img src="{{asset('frontend/images/logo-footer.png')}}" alt="">
                
                </div>
                <p class="footer-text pt-5">Because the world believes our lives will be focused on booking in 5 years, we decided to create a booking platform.<p>
            
            </div>
                <div class="col  ">
                    <h3>CUSTOMER APP</h3>
                    
                    <ul>
                    
                    <li>
                        <a href="{{setting('android_customer_app_link')}}"><img src="{{asset('frontend/images/google-app.svg')}}" alt=""></a>
                    </li>
                    <li>
                        
                    <a href="{{setting('ios_customer_app_link')}}"><img src="{{asset('frontend/images/apply-app.svg')}}" alt=""></a>
                    </li>
                    
                    
                    </ul>
                </div>
                                <div class="col  ">
                    <h3>Provider APP</h3>
                    <ul>
                    
                    <li>
                        <a href="{{setting('android_provider_app_link')}}"><img src="{{asset('frontend/images/google-app.svg')}}" alt=""></a>
                    </li>
                    <li>
                        
                    <a href="{{setting('ios_provider_app_link')}}"><img src="{{asset('frontend/images/apply-app.svg')}}" alt=""></a>
                    </li>
                    
                    </ul>
                </div>
                                <div class="col  ">
                    <h3>Company</h3>
                    <ul>
                    <li>
                    <a href="{{route('/')}}">Home
</a>
                    </li>
                    <li>
                    <a href="{{route('privacy-policy')}}">Privacy Policy</a>
                    </li>
                    <li>
                    <a href="{{route('term-condition')}}">Terms And Conditions</a>
                    </li>
                     <li>
                    <a href="{{route('aboutUs')}}">About Us</a>
                    </li>
                    </ul>
                </div>
                    <div class="col  ">
                    
                        <div class="socail-footer ">
                            <span>Follow Us</span>
                            <br>
                              <br>
                            <ul>
                            <li>
                            <a href="{{setting('facebook_url')}}"><img src="{{asset('frontend/images/fb.svg')}}" alt=""> </a>
                            </li>
                            <li>
                            <a href="{{setting('twitter_url')}}"><img src="{{asset('frontend/images/twitter.svg')}}" alt=""></a>
                            </li>
                            <li>
                            <a href="{{setting('instagram_url')}}"><img src="{{asset('frontend/images/inta.svg')}}" alt=""></a>
                            </li>
            
            
            </ul>
        </div>
                </div>
            </div>
        </div>
        <div class="copyright py-5 text-center mt-20">
        <div class="container">
    Copyright © {{Date('Y')}} JustSayWhat. All Rights Reserved.
        </div>
        </div>
    </section>

<script src="{{asset('frontend/js/jquery-1.12.0.min.js')}}"></script>
<script src="{{asset('frontend/js/bootstrap.min.js')}}"></script>
<script src="{{asset('admin/js/sweetalert2.min.js')}}"></script>


@yield('third_party_scripts')

@stack('page_scripts')

@yield('after_footer')

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
        easing: 'ease-in-out-sine'
    });



  window.onscroll = function() {myFunction()};

  var header = document.getElementById("myHeader");
  var sticky = header.offsetTop;

  function myFunction() {
  if (window.pageYOffset > sticky) {
  header.classList.add("sticky");
  } else {
  header.classList.remove("sticky");
  }
  }
</script>
</body>
</html>
