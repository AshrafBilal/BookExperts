@extends('layouts.admin')
@section('content')

	<link rel="stylesheet" href="{{asset('admin/css/post_style.css')}}">
    <link rel="stylesheet" href="{{asset('admin/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/css/owl.theme.default.min.css')}}">
  

 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Post</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('posts.index')}}">Posts</a></li>
              <li class="breadcrumb-item active">Post Details</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
       <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
         
          <!-- /.col -->
            <!-- About Me Box -->
            <div class="col-md-12">
              <div class="card-header">
                <h3 class="card-title">Post Details</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong>Added By</strong>

                <p class="text-muted">
                  {{$post->user->full_name}}
                </p>

                <hr>
                
             
                <strong>Description</strong>

                <p class="text-muted">{{$post->description}}</p>

                <hr>
                
             

              </div>
              <!-- /.card-body -->
            </div>

          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    
    
  <section class="testminal_outer">
         <div class="container">
            <div class="row">
            @if(!$images->isEmpty())
               <div class="col-md-4">
                  <div class="owl-carousel owl-theme owl-loaded owl-drag">
                     <div class="owl-stage-outer">
                        <div class="owl-stage" style="transform: translate3d(-844px, 0px, 0px); transition: all 0.25s ease 0s; width: 2110px;">
                              @foreach($images as $image)
                           <div class="owl-item" style="width: 402px; margin-right: 20px;">
                              <div class="item ustom_item">
                                 <img src="{{$image->file}}">
                              </div>
                           </div>
                           @endforeach
                        </div>
                     </div>
                  </div>
               </div>
               @endif
               @if(!$videos->isEmpty())
              <div class="col-md-4">
                  <div class="owl-carousel owl-theme owl-loaded owl-drag">
                     <div class="owl-stage-outer">
                        <div class="owl-stage" style="transform: translate3d(-844px, 0px, 0px); transition: all 0.25s ease 0s; width: 2110px;">
                          @foreach($videos as $video)
                           <div class="owl-item" style="width: 402px; margin-right: 20px;">
                              <div class="item ustom_item">
                              	<video width="100%" height="210px" controls>
									<source
										src="{{$video->file}}"
										type="video/mp4">
								</video>
                              </div>
                           </div>
                           @endforeach
                        </div>
                     </div>
                  </div>
               </div>
               @endif
               @if(!$songs->isEmpty())
               <div class="col-md-4">
                  <div class="owl-carousel owl-theme owl-loaded owl-drag">
                     <div class="owl-stage-outer">
                        <div class="owl-stage" style="transform: translate3d(-844px, 0px, 0px); transition: all 0.25s ease 0s; width: 2110px;">
                           
                         @foreach($songs as $song)
                           <div class="owl-item" style="width: 402px; margin-right: 20px;">
                              <div class="item ustom_item">
                                 <audio controls>
                                    <source src="{{$song->file}}" type="audio/mpeg">
                                 </audio>
                              </div>
                           </div>
                        @endforeach
                          
                        </div>
                     </div>
                  </div>
               </div>
               @endif
            </div>
         </div>
      </section>
 
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection
		
@section('scripts')

 <script>
         $(document).ready(function() {
           $('.owl-carousel').owlCarousel({
         autoplay:true,
             loop: true,
             margin: 20,
             responsiveClass: true,
             responsive: {
               0: {
                 items: 1,
                 nav: true
               },
               600: {
                 items: 1,
                 nav: false
               },
               1000: {
                 items: 1,
                 nav: true,
                 loop: false,
                 margin: 20
               }
             }
           })
         })
      </script>
@endsection