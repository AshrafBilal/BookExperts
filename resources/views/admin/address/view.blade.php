@extends('layouts.admin')
@section('content')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Address</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('address.index')}}">Address</a></li>
              <li class="breadcrumb-item active">Address Details</li>
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
                <h3 class="card-title">Address Details</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong>Added By</strong>

                <p class="text-muted">
                  {{@$address->user->full_name}}
                </p>

                <hr>
                
             
                <strong>Name For Address</strong>

                <p class="text-muted">{{$address->first_name}}</p>

                <hr>
                  <strong>Phone Number</strong>

                <p class="text-muted">{{$address->phone_code}}{{$address->phone_number}}</p>

                <hr>
                 <strong>Address</strong>

                <p class="text-muted">{{$address->address1}}</p>

                <hr>
                    <strong>Street</strong>

                <p class="text-muted">{{$address->street}}</p>

                <hr>
                  <strong>City</strong>

                <p class="text-muted">{{$address->city}}</p>

                <hr>
                
                   <strong>Country</strong>

                <p class="text-muted">{{$address->country}}</p>

                <hr>
                
                     <strong>Zip Code</strong>

                <p class="text-muted">{{$address->zip_code}}</p>

                <hr>
                
                
                     <strong>Default address</strong>

                <p class="text-muted">{{!empty($address->default_address)?'Yes':'No'}}</p>

                <hr>

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