@extends('layouts.admin')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Services</h1>
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
                <table id="servicesTable" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Service Name</th>
                    <th>Home Visit Price</th>
                    <th>Work Place Price</th>
                    <th>Time (minutes)</th>
                    <th>Added By</th>
                    <th>Service Provider</th>
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
    $('#servicesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{route('services.index')}}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'name', name: 'name' },
            { data: 'home_service_price', name: 'home_service_price' },
            { data: 'price', name: 'price' },
            { data: 'time', name: 'time' },
            { data: 'user_id', name: 'user_id' },
            { data: 'service_provider_id', name: 'service_provider_id' },
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ],
    });

    
$(document).on('click', '.delete-datatable-record', function(e){
    let url  = site_url + "/admin/services/" + $(this).attr('data-id');
    let tableId = 'servicesTable';
    deleteDataTableRecord(url, tableId);
});

});





			</script>
	     
@endsection