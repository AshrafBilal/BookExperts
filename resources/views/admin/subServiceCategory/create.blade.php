@extends('layouts.admin')
@section('content')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add Sub Service Category</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('subServiceCategory.index')}}">Sub Service Categories</a></li>
              <li class="breadcrumb-item active"> Add Sub Service Category</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->

     <section class="content">
      <div class="container-fluid">


     <div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
        </br>
      </br>
           
            <div class="card-body">
       <form method="post" action="{{route('subServiceCategory.store')}}"  id="add_service_category_form" class="common_form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="name" class="col-form-label float-right">Category Name <span class="text-danger asteric-sign">&#42;</span></label>
                        </div>
                        <div class="col-md-6">
                            <input id="name" type="text" placeholder="Enter Category Name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}">
                            @if ($errors->has('name'))
                                <span class="invalid-feedback_select" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>  

                      

                   <div class="form-group row">
                        <div class="col-md-3">
                            <label for="service_category_id" class="col-form-label float-right">Category Type <span class="text-danger asteric-sign">&#42;</span></label>
                        </div>

                        <div class="col-md-6">
                        <select
                    class="custom-select required category_drop_down select2" name="service_category_id" id="category_drop_down">
                    <option disabled selected>Select Service category</option>
                    @foreach($categories as $key=> $cat)
                      @if(old('service_category_id') ==$key )
                          <option value="{{$key}}" selected>{{$cat}}</option> 
                          @else
                          <option value="{{$key}}">{{$cat}}</option> 
                          @endif
                    @endforeach
                  </select> 
                            @if ($errors->has('service_category_id'))
                                <span class="invalid-feedback_select" role="alert" id="service_category_id_error">
                                    <strong>{{ $errors->first('service_category_id') }}</strong>
                                </span>
                                @else
                                 <span class="invalid-feedback_select" role="alert" id="service_category_id_error">
                                    <strong>{{ $errors->first('service_category_id') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>      
                    
                    
                    <div class="form-group row">
                            <div class="col-md-3">
                                <label for="service_category_id" class="col-form-label float-right">Category Image <span class="text-danger asteric-sign">&#42;</span></label>
                            </div>
    
                            <div class="col-md-6">
                        <div class="input-group">
                          <div class="custom-file">
                            <input type="file" name="file_path" class="custom-file-input" id="sub_cat_img">
                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                          </div>
                        </div>
                		@if($errors->has('file_path'))
                        <span class="invalid-feedback_select" id="sub_cat_img_error" role="alert">
                    		<strong>{{ $errors->first('file_path') }}</strong>
                     </span> 
                    		@else
                         <span class="invalid-feedback_select" id="sub_cat_img_error" role="alert">
                    		<strong id="file_path_error">{{ @$errors->first('file_path') }}</strong>
                    	</span>
                    	@endif
                            </div>

                    </div>      
                    
                      <div class="form-group row">
                       <div class="col-md-3">
                                <label for="service_category_id" class="col-form-label float-right">Category Image Preview<span class="text-danger asteric-sign">&#42;</span></label>
                            </div>
					  <div class="col-md-6">
						<div class="blog-imagebox">
						<img id="blog_image" class="show_image_preview" src="{{asset('admin/images/default_image.png')}}" default_url="{{asset('admin/images/default_image.png')}}" alt="blog_image">
						</div>
					</div>
				  </div> 
                         
            
                    <div class="form-group row">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary my-4">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</section>
   
  </div>
  <!-- /.content-wrapper -->
 
  
  @endsection
  
  
  
  @section('after_footer')
   <script type="text/javascript">
 	
	
	 $(".select2").select2();
	
  </script>
 
  @endsection