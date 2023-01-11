@extends('layouts.admin')
@section('content')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">	
          <div class="col-sm-6">
            <h1>Booking</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('bookings.index')}}">Bookings</a></li>
              <li class="breadcrumb-item active">Booking Details</li>
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
                <h3 class="card-title">Booking Details</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong>Order ID </strong>
   				<p class="text-muted">
                  {{$booking->order_id}}
                </p>

                <hr>
                   <strong>Booked By </strong>
                <p class="text-muted">
                  {{@$booking->user->full_name}}
                </p>

                <hr>
                
             
                <strong>Booking Type</strong>

                <p class="text-muted">{{$booking->getBookingType()}}</p>
    			<hr>
    			
    			 <strong>Live Booking</strong>
                <p class="text-muted">{{!empty($booking->is_live_booking)?'Yes':'No'}}</p>
               
                
                
                 <hr>
                   <strong>Payment Method</strong>

                <p class="text-muted">{{$booking->getPaymentMethod()}}</p>

                <hr>
    			
                 <strong>Booking Type</strong>
                <p class="text-muted">{{$booking->getBookingType()}}</p>
               
                <hr>
                
                  <strong>Booking Date Time</strong>
                <p class="text-muted">{{changeTimeZone($booking->booking_date_time)}}</p>
               
                <hr>
                   <strong>Status</strong>

                <p class="text-muted">{{$booking->getBookingStatus()}}</p>

                <hr>
                   <strong>Total Service</strong>

                <p class="text-muted">{{$booking->total_quanity}}</p>

                <hr>
                   <strong>Total Amount</strong>

                <p class="text-muted">{{$booking->total_amount}}</p>

                <hr>
                   <strong>Service Provider</strong>

                <p class="text-muted">{{$booking->servicePRovider->full_name}}</p>

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