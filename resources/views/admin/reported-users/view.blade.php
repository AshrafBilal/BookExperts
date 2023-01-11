@extends('layouts.admin')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Reported Users</h1>
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
                     <th>Report By</th>
                     <th>Report type</th>
                     <th>Report Comment</th>
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
        ajax: "{{route('reportedUsers.show',base64_encode($user->id))}}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'user_id', name: 'user_id' },
            { data: 'report_type', name: 'report_type' },
            { data: 'comment', name: 'comment' },
            
        ],
       
    });
});




			</script>
	     
@endsection