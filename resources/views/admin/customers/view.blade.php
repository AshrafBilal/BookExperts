@extends('layouts.admin')
@section('content')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('customers.index')}}">Customers</a></li>
              <li class="breadcrumb-item active">Customer Details</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="_profile-user-img img-fluid img-circle_"
                       src="{{$customer->getProfilePhoto()}}"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">{{$customer->first_name}} {{$customer->last_name}}</h3>

                <p class="text-muted text-center">Customer</p>

            

              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
		
         
            <!-- /.card -->
          </div>
          <!-- /.col -->
            <!-- About Me Box -->
            <div class="col-md-9">
              <div class="card-header">
                <h3 class="card-title">Profile Details</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong>Email</strong>

                <p class="text-muted">
                  {{$customer->email}}
                </p>

                <hr>
                
                 <strong>Phone Number</strong>

                <p class="text-muted">
                  {{$customer->phone_code}}{{$customer->phone_number}}
                </p>

                <hr>
                
             
                
                 <strong>Account Created At</strong>

                <p class="text-muted">
                  {{changeTimeZone($customer->created_at)}}
                </p>


              </div>
              <!-- /.card-body -->
            </div>

          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection