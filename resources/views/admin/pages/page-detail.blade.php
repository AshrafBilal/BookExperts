@extends('layouts.admin')
@section('content')
@section('title',' Page Details')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
       <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Page Details</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('pages')}}">Pages</a></li>
              <li class="breadcrumb-item active">Page Details </li>
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
          		            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"> Page Details</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
             
                <div class="card-body">
                  <div class="form-group">
                  <label>Title</label>
                  <p class="text-muted">{{$page->title}}</p>
                  </div>
                  
                    <div class="form-group">
                  <label>Page Type</label>
                  <p class="text-muted">{{$page->getPageType()}}</p>
                  </div>
                
                <div class="form-group">
                        <label>Description</label>
                     <p class="text-muted">{!!$page->description!!}</p>
                </div>
                 
                <!-- /.card-body -->

            
                </div>
            </div>
          	</div>
       </div>    
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  
@endsection