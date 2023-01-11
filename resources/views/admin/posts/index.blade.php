@extends('layouts.admin')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Posts</h1>
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
              
                <table id="postsTable" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Added By</th>
                    <th>File Type</th>
                     <th>Post Type</th>
                    <th>Description</th>
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
	
    $('#postsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{route('posts.index')}}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'user_id', name: 'user_id' },
            { data: 'file_type', name: 'file_type' },
            { data: 'post_type', name: 'post_type' },
            { data: 'description', name: 'description' },
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ],
       
    });
});


$(document).on('click', '.delete-datatable-record', function(e){
    let url  = site_url + "/admin/posts/" + $(this).attr('data-id');
    let tableId = 'postsTable';
    deleteDataTableRecord(url, tableId);
});

			</script>
	     
@endsection