@extends('layouts.admin')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add Page</h1>
          </div>
      
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
          		            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Add Page</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" id="add_page_form" enctype="multipart/form-data">
                @method('POST')
				 @csrf
                <div class="card-body">
                  <div class="row">
                  <div class="form-group col-md-6">
                    <label for="exampleInputEmail1">Title</label>
                    <input type="text" class="form-control"  name="title" id="exampleInputEmail1" value="{{old('title')}}" placeholder="Enter title">
                   <span class="error" id="title_error">
            		@if($errors->has('title'))
                		{{ $errors->first('title')}}
                	@endif
                 </span> 
                  </div>
                  
                   <div class="form-group col-md-6">
                    <label for="exampleInputEmail1">Page Type</label>
						<select class="custom-select" name="page_type">
						<option value="1">About Us</option>
						<option value="2">Privacy Policy</option>
						<option value="3">Terms and Condition</option>
						</select>
                   <span class="error">
            		@if($errors->has('page_type'))
                		{{ $errors->first('page_type')}}
                	@endif
                 </span> 
                  </div>
                </div>
                <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control ckeditor" name="description" rows="6" placeholder="Enter Description...">{{old('description')}}</textarea>
                 <span class="error" id="description_error">
            		@if($errors->has('description'))
                		{{ $errors->first('description')}}
                	@endif
                 </span> 
                </div>
                 
                <!-- /.card-body -->

                <div class=" text-center mt-4">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </div>
              </form>
            </div>
          	</div>
       </div>    
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  
@endsection