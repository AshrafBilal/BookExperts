@extends('layouts.admin')
@section('content')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
              <li class="breadcrumb-item active"> Edit Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
       <form method="post" id="admin_edit_profile_form" class="common_form" enctype="multipart/form-data">
				 @method('POST')
				 @csrf
        <div class="col-md-6">
          <div class="card card-primary">
         
            <div class="card-body">
              <div class="form-group">
                <label for="inputName">Full Name</label>
                <input type="text" id="inputName" class="form-control" name="full_name" value="{{!empty(old('full_name'))?old('full_name'):$user->full_name}}">
                 <span class="error">
            		@if($errors->has('full_name'))
                		{{ $errors->first('full_name') }}
                	@endif
                 </span> 
              </div>
                <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" class="form-control" value="{{!empty(old('email'))?old('email'):$user->email}}">
                <span class="error">
            		@if($errors->has('email'))
                		{{ $errors->first('email') }}
                	@endif
                 </span> 
              </div>
                 <div class="form-group">
                <label for="mobile_number">Mobile Number</label>
                <input type="text" id="mobile_number" class="form-control number_only" name="phone_number" value="{{$user->phone_number}}">
             <span class="error">
            		@if($errors->has('phone_number'))
                		{{ $errors->first('phone_number') }}
                	@endif
                 </span> 
              </div>
              
            <div class="form-group">
                <label for="password">Password</label>
                <input type="text" id="password" class="form-control" name="password" value="">
             <span class="error">
            		@if($errors->has('password'))
                		{{ $errors->first('password') }}
                	@endif
                 </span> 
              </div>
         
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        
         <div class="col-md-6">
          <div class="card card-primary">
         
            <div class="card-body card-2">
            
            <div class="form-group">
                    <label for="exampleInputFile">Profile File</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" name="profile_file" class="custom-file-input" id="profile_input_file">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                    </div>
                    <span class="error">
            		@if($errors->has('profile_file'))
                		{{ $errors->first('profile_file') }}
                	@endif
                 </span> 
                  </div>
                <div class="form-group">
                <label for="inputName">Location</label>
                <input type="text" id="inputName" class="form-control autocomplete searchInput" name="address" value="{{$user->address}}">
                  <input type="hidden" name="latitude" id="latitude" value="{{$user->latitude}}">
           	     <input type="hidden" name="longitude" id="longitude" value="{{$user->longitude}}">
             <span class="error">
            		@if($errors->has('address'))
                		{{ $errors->first('address') }}
                	@endif
                 </span> 
              </div>
              <div class="form-group">
                <label for="inputDescription">About me</label>
                <textarea id="inputDescription" name="about_me" class="form-control" rows="4">{{$user->about_me}}</textarea>
              <span class="error">
            		@if($errors->has('about_me'))
                		{{ $errors->first('about_me') }}
                	@endif
                 </span> 
              </div>
          
         
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <div class="col-12  text-center">
          <input type="submit" value="Save Changes" class="btn btn-success float-right">
        </div>
    
		</form>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection