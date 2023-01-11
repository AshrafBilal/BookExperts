@extends('layouts.admin')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Customers</h1>
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
              
                <table id="customersTable" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                     <th>Phone Number</th>
                     <th>Active Status</th>
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
	
    $('#customersTable').DataTable({
        processing: true,
        serverSide: true,
       // ajax: "{{route('customers.index')}}",
        ajax: {
                url: '{{ route('customers.index') }}',
                    data: function (d) {
                    d.keyword = $('.form-control-sm').val();
                }
            },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'full_name', name: 'full_name' },
            { data: 'email', name: 'email' },
            { data: 'phone_number', name: 'phone_number' },
            { data: 'active_status', name: 'active_status' , searchable: false},
            //{ data: 'created_at', name: 'created_at', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ],
       
    });
});


$(document).on('click', '.delete-datatable-record', function(e){
    let url  = site_url + "/admin/customers/" + $(this).attr('data-id');
    let tableId = 'customersTable';
    deleteDataTableRecord(url, tableId);
});




			</script>
	     
@endsection