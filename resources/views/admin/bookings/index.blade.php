@extends('layouts.admin')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Bookings</h1>
          </div>
           <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <!-- <li class="breadcrumb-item active">DataTables</li> -->
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
            
              <!-- /.card-header -->
              <div class="card-body">
                   <div class="table-responsive">
                <table id="bookingsTable" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Order ID</th>
                     <th>Booked By</th>
                    <th>Status</th>
                    <th>Booking Type</th>
                    <th>Live Booking</th>
                     <th>Payment Method</th>
                     <th>Booking Date Time</th>
                     <th>Total Service</th>
                    <th>Total Amount</th>
                    <th data-orderable="false">Action</th>
                  </tr>
                  </thead>
              
                </table>
                </div>
              </div>
          
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
		</div>
       </div>    
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
   
@endsection

@section('scripts')
	     @include('includes.data-table-scripts')   

	         <script>

$(document).ready(function() {
	
    $('#bookingsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{route('bookings.index')}}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'order_id', name: 'order_id' },
            { data: 'user_id', name: 'user_id' },
            { data: 'status', name: 'status' },
            { data: 'booking_type', name: 'booking_type' },
            { data: 'is_live_booking', name: 'is_live_booking' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'booking_date_time', name: 'booking_date_time' },
            { data: 'total_quanity', name: 'total_quanity' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ],
       
    });
});


$(document).on('click', '.delete-datatable-record', function(e){
    let url  = site_url + "/admin/bookings/" + $(this).attr('data-id');
    let tableId = 'bookingsTable';
    deleteDataTableRecord(url, tableId);
});

			</script>
	     
@endsection