@extends('layouts.admin')
@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Pages</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li><a href="{{route('pages.addPage')}}" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Add Page</a></li>
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
                <table id="service_table" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Page Type</th>
                    <th>Description</th>
                    <th data-orderable="false">Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  @if(!empty($pages->count()))
                       @foreach($pages as $key=>$page)
                           <tr>
                                <td>{{$key+1}}</td>
                                <td>{{$page->title}}</td>
                                <td>{{$page->getPageType()}}</td>
                                <td>{!!mb_strimwidth($page->description, 0, 50, "...")!!}</td>
                                <td>
                                     <a href="{{route('pages.pageDetails',base64_encode($page->id))}}"  ><i class="fa fa-eye"></i></a> 
        						    <a href="{{route('pages.updatePage',base64_encode($page->id))}}"  class="ml-2"><i class="fas fa-edit mr-1" aria-hidden="true"></i></a>
				
                                </td>
                            </tr>
                      @endforeach  
                 @else
                   <tr class="text-center"><td colspan="5">No page added.</td></tr>
                 @endif 
                  </tbody>
               
                </table>
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
   <form action="{{ route('pages.deletePage') }}" method="post" id="delete_page_form">
	      @method('DELETE')
	      @csrf
	 	      	<input type="hidden" name="page_id" id="page_id" >
	 
	        </form>
	        
@endsection

@section('scripts')
	     @include('includes.data-table-scripts')   
	         <script>
  $(function () {
    $("#service_table").DataTable({
    //  dom: 'Bfrtip',
      "responsive": true, "paging": true,"lengthChange": false, "autoWidth": false, "searching": true,
     // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>
	     
@endsection