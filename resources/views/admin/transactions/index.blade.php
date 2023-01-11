@extends('layouts.admin')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Transactions</h1>
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
              
                <table id="AddresssTable" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Booking ID</th>
                     <th>Transaction ID	</th>
                    <th>Provider Amount</th>
                    <th>Admin Amount</th>
                    <th>Total Amount</th>
                    <th>Payment Status</th>
                    <th>Payment For</th>
                     <th>Payment Method</th>
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
	
    $('#AddresssTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{route('transactions.index')}}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'user_id', name: 'user_id' },
            { data: 'booking_id', name: 'booking_id' },
            { data: 'transaction_id', name: 'transaction_id' },
            { data: 'amount', name: 'amount' },
            { data: 'commission_amount', name: 'commission_amount' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'status', name: 'status' },
            { data: 'type', name: 'type' },
            { data: 'payment_mode', name: 'payment_mode' },
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ],
       
    });
});


$(document).on('click', '.delete-datatable-record', function(e){
    let url  = site_url + "/admin/transactions/" + $(this).attr('data-id');
    let tableId = 'AddresssTable';
    deleteDataTableRecord(url, tableId);
});

			</script>
	     
@endsection