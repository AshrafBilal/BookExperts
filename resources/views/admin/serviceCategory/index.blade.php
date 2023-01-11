@extends('layouts.admin')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Service Categories</h1>
          </div>
           <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li><a href="{{route('serviceCategory.create')}}" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Add Service Category</a></li>
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
                <table id="serviceCategoriesTable" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                     <th>Category Type</th>
                    <th>Created At</th>
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
    $('#serviceCategoriesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{route('serviceCategory.index')}}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'name', name: 'name' },
            { data: 'category_type', name: 'category_type' },
            { data: 'created_at', name: 'created_at', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ],
    });

    
$(document).on('click', '.delete-datatable-record', function(e){
    let url  = site_url + "/admin/serviceCategory/" + $(this).attr('data-id');
    let tableId = 'serviceCategoriesTable';
    deleteDataTableRecord(url, tableId);
});

});





			</script>
	     
@endsection