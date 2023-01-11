@extends('layouts.admin')
@section('content')



  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
        
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{totalUsers()}}</h3>

                <p>Total Customers</p>
              </div>
              <div class="icon">
                   <i class="fa fa-users" aria-hidden="true"></i>
              </div>
              <a href="{{route('customers.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{totalServiceProviders()}}</h3>

                <p>Total Service Providers</p>
              </div>
              <div class="icon">
               <i class="fa fa-users" aria-hidden="true"></i>
              </div>
              <a href="{{route('serviceProviders.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{totalServiceCategories()}}</h3>

                <p>Total Services Categories</p>
              </div>
              <div class="icon">
             <i class="fas fa-th-list"></i>
              </div>
              <a href="{{route('serviceCategory.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{totalPosts()}}</h3>

                <p>Total Posts </p>
              </div>
              <div class="icon">
<i class="far fa-list-alt"></i>
              </div>
              <a href="{{route('posts.index')}}" class="small-box-footer"> More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        
        
                   <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{totalPendingAccountHomeServices()}}</h3>

                <p>Total Pending Account For <br> Approval Home Services </p>
              </div>
              <div class="icon">
                   <i class="fa fa-spinner" aria-hidden="true"></i>
              </div>
              <a href="{{route('pendingAccount')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{totalApprovedAccountHomeServices()}}</h3>

                <p>Total Approved Account <br>For Home Services
</p>
              </div>
              <div class="icon">
               <i class="fa fa fa-check" aria-hidden="true"></i>
              </div>
              <a href="{{route('approvedAccount')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{totalIndividualServiceProviders()}}</h3>

                <p>Total Individual <br>Service Providers
                </p>
              </div>
              <div class="icon">
             <i class="fa fa-wrench"></i>
              </div>
              <a href="{{route('individualAccount')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{totalBusinessServiceProviders()}}</h3>

                 <p>Total Business <br>Service Providers
                </p>
              </div>
              <div class="icon">
<i class="fas fa-business-time"></i>
              </div>
              <a href="{{route('businessAccount')}}" class="small-box-footer"> More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>          
         
        
          <div class="row">
          <div class="col-lg-12">
            <div class="card">
            
              <div class="card-body" id="admin_container">

              </div>
            </div>
            <!-- /.card -->

            <!-- /.card -->
          </div>
          
        </div>
        
        
        <!-- /.row -->
        <!-- Main row -->

        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2020-2021 <a href="{{url('/')}}">{{env('APP_NAME')}}</a>.</strong>
    All rights reserved.
  
  </footer>


@endsection

@section('scripts')
 <script src="https://code.highcharts.com/highcharts.js"></script>
 <script>


	const chart = Highcharts.chart('admin_container', {
  title: {
    text: 'Monthly Users Registered'
  },
  subtitle: {
    text: 'Build'
  },
   credits: {
    enabled: false
},
 
  xAxis: {
    categories: [ <?php echo "'".implode("','", array_keys($userData))."'"?>]
  },
   yAxis: {
        min: 0,
        title: {
          text: 'Count'
        }
    },
  series: [{
  	'name':'Total Registered ',
  	 type: 'column',
    data: [<?php echo implode(",", $totalData)?>],
    showInLegend: false
  },{
     name: "Total Users",
    color: "#00FF00",
    type: 'column',
    data: [<?php echo implode(",", $userData)?>],
    showInLegend: false
  },{
    name: 'Total Service Providers',
     type: 'column',
    data: [<?php echo implode(",", $providerData)?>],
    showInLegend: false
  }]
});




	
	    </script>

 
 @endsection