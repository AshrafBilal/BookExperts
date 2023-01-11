  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
  <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">     
  
      <li class="dropdown user user-menu open">
            <a href="#" id="dropdown-toggle" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
              <span class="hidden-xs">{{@Auth::user()->full_name}} </span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">

                <p>
                    <img src="{{$user->getProfilePhoto()}}" width="150px" height="150px" class="img-circle elevation-2" alt="User Image">
                 </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer dropdown user user-menu open">
                <div class="pull-left">
                  <a href="{{route('adminProfile')}}" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="javascript:;"  onclick="confirmLogout()" class="btn btn-default btn-flat">Logout</a>
                </div>
              </li>
            </ul>
          </li>
    </ul>
  </nav>
  <!-- /.navbar -->
     
  