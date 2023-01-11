@extends('layouts.admin')
@section('content')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Transaction</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('transactions.index')}}">Transaction</a></li>
              <li class="breadcrumb-item active">Transaction Details</li>
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
                <h3 class="card-title">Transaction Details</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong>Customer</strong>

                <p class="text-muted">
                  {{$transaction->user->full_name}}
                </p>

                <hr>
                
             
              
                  <strong>Booking ID</strong>

                <p class="text-muted">{{$transaction->booking_id}}</p>

                <hr>
               <strong>Transaction ID</strong>

                <p class="text-muted">{{$transaction->transaction_id}}</p>

                <hr>
                
                 <strong>Service Provider Amount</strong>

                <p class="text-muted">{{$transaction->amount}}</p>
 				<hr>
                
                 <strong>Admin Amount</strong>

                <p class="text-muted">{{$transaction->commission_amount}}</p>
 				<hr>
                
                 <strong>Total Amount</strong>

                <p class="text-muted">{{$transaction->total_amount}}</p>

                <hr>
                
                 <strong>Card ID</strong>

                <p class="text-muted">{{$transaction->card_id}}</p>

               
                <hr>
                  <strong>Status</strong>

                <p class="text-muted">{{!empty($transaction->status)?'success':'Failed'}}</p>

                <hr>
                 
                 <strong>Payment For</strong>

                <p class="text-muted">{{$transaction->getPaymentType()}}</p>

                <hr>
                
                 
                 <strong>Payment Method</strong>

                <p class="text-muted">{{$transaction->getPaymentMode()}}</p>

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