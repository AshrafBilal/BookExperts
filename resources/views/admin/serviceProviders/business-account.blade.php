@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-8">
            <h1>Business Service Provider</h1>
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
                <table id="serviceProviderTable" class="table table-bordered table-hover custom_table">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Unique ID</th>
                    <th>Account Status</th>
                    <th>Profile Status</th>
                    <th>Profile Status Action</th>
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
	
    $('#serviceProviderTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{route('businessAccount')}}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'full_name', name: 'full_name' },
            { data: 'email', name: 'email' },
            { data: 'phone_number', name: 'phone_number' },
            { data: 'unique_id', name: 'unique_id' },
            { data: 'active_status', name: 'active_status' , searchable: false},
            { data: 'profile_verified', name: 'profile_verified' , searchable: false},
            { data: 'profile_status_action', name: 'profile_status_action' , searchable: false},
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ],
       
    });
});

$(document).on('click', '.delete-datatable-record', function(e){
    let url  = site_url + "/admin/serviceProviders/" + $(this).attr('data-id');
    let tableId = 'serviceProviderTable';
    deleteDataTableRecord(url, tableId);
});



			</script>
	     
@endsection