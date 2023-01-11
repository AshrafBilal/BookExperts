@extends('layouts.admin')
@section('content')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Update Service Category</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('serviceCategory.index')}}">Service Categories</a></li>
              <li class="breadcrumb-item active"> Update Service Category</li>
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
           
            <div class="card-body">
 <form method="post" action="{{ route('serviceCategory.update', base64_encode($service->id)) }}"  id="add_service_category_form" class="common_form" enctype="multipart/form-data">
         @method('PUT')
         @csrf                 
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="name" class="col-form-label float-right">Category Name <span class="text-danger asteric-sign">&#42;</span></label>
                        </div>
                        <div class="col-md-6">
                            <input id="name" type="text" placeholder="Enter Category Name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{!empty(old('first_name'))?old('first_name'):@$service->name}}">
                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>  

                      

                   <div class="form-group row">
                        <div class="col-md-3">
                            <label for="category_type" class="col-form-label float-right">Category Type <span class="text-danger asteric-sign">&#42;</span></label>
                        </div>

                        <div class="col-md-6">

                        <select class="form-control {{ $errors->has('category_type') ? ' is-invalid' : '' }}" id="category_type" name="category_type">
                        <option value="1" {{($service->category_type == 1)?'selected':''}}>Normal</option> 
                        <option value="2" {{($service->category_type == 2)?'selected':''}}>Other</option> 
                      </select>
                           
                            @if ($errors->has('category_type'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('category_type') }}</strong>
                                </span>
                            @endif
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
</section>>
    
  </div>
  <!-- /.content-wrapper -->
 
  
  @endsection
