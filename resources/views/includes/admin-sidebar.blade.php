<?php
use Illuminate\Support\Facades\Route;
?>


  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link">
      <img src="{{asset('admin/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image">
      <span class="brand-text font-weight-light">&nbsp;</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">   
     <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
  		<li class="nav-item ">
                <a href="{{route('home')}}" class="nav-link">
                 <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>Dashboard</p>
                </a>
         </li>
        
       
        </ul>
      </nav>
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
         
       
           <li class="nav-item  {{request()->routeIs('customers.*') ? 'active' : ''  }}">
           <a href="{{route('customers.index')}}" class="nav-link">
                  <i class="nav-icon fas fa-users"></i>
                  <p>Customers</p>
                </a>
         </li>
         
           <li class="nav-item  {{ request()->routeIs('serviceProviders.*') ? 'active' : '' }}">
           <a href="{{route('serviceProviders.index')}}" class="nav-link">
                  <i class="nav-icon fas fa-users"></i>
                  <p>Service Providers</p>
                </a>
         </li>
         
           <li class="nav-item  {{ (Route::current()->getName() == 'pendingAccount') ? 'active' : '' }}">
           <a href="{{route('pendingAccount')}}" class="nav-link">
                  <i class="nav-icon fa fa-spinner"></i>
                  <p>Pending Accounts For Approval Home Services</p>
                </a>
         </li>
         
           <li class="nav-item  {{(Route::current()->getName() == 'approvedAccount')  ? 'active' : '' }}">
           <a href="{{route('approvedAccount')}}" class="nav-link">
                  <i class="nav-icon fa fa-check"></i>
                  <p>Approved Accounts For Home Services</p>
                </a>
         </li>
         
           <li class="nav-item  {{ (Route::current()->getName() == 'individualAccount')  ? 'active' : '' }}">
           <a href="{{route('individualAccount')}}" class="nav-link">
                  <i class="nav-icon fa fa-wrench"></i>
                  <p>Total Individual
Service Providers</p>
                </a>
         </li>
         
           <li class="nav-item  {{ (Route::current()->getName() == 'businessAccount')  ? 'active' : '' }}">
           <a href="{{route('businessAccount')}}" class="nav-link">
                  <i class="nav-icon fas fa-business-time"></i>
                  <p>Total Business
Service Providers</p>
                </a>
         </li>
         
           <li class="nav-item  {{ request()->routeIs('serviceCategory.*') ? 'active' : ''}}">
           <a href="{{route('serviceCategory.index')}}" class="nav-link">
                  <i class="nav-icon fas fa-th-list"></i>
                  <p>Service Category</p>
                </a>
         </li>
         
         
           <li class="nav-item  {{ request()->routeIs('subServiceCategory.*') ? 'active' : ''}}">
           <a href="{{route('subServiceCategory.index')}}" class="nav-link">
                  <i class="nav-icon far fa-list-alt"></i>
                  <p>Sub Service Category</p>
                </a>
         </li>
         
         
         
           <li class="nav-item  {{ request()->routeIs('posts.*') ? 'active' : '' }}">
           <a href="{{route('posts.index')}}" class="nav-link">
                  <i class="nav-icon fa fa-sticky-note-o"></i>
                  <p>Posts</p>
                </a>
         </li>
         
           <li class="nav-item  {{ request()->routeIs('reportedPosts.*') ? 'active' : '' }}">
           <a href="{{route('reportedPosts.index')}}" class="nav-link">
                  <i class="nav-icon fa fa fa-file"></i>
                  <p>Reported Posts</p>
                </a>
         </li>
         
           <li class="nav-item  {{ request()->routeIs('reportedUsers.*') ? 'active' : '' }}">
           <a href="{{route('reportedUsers.index')}}" class="nav-link">
                  <i class="nav-icon fa fa fa-file-o"></i>
                  <p>Reported Users</p>
                </a>
         </li>
         
          <li class="nav-item  {{ request()->routeIs('services.*') ? 'active' : '' }}">
           <a href="{{route('services.index')}}" class="nav-link">
                  <i class="nav-icon fa fa-wrench"></i>
                  <p>Services</p>
                </a>
         </li>
         
           <li class="nav-item  {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
           <a href="{{route('bookings.index')}}" class="nav-link">
                  <i class="nav-icon fa fa-ticket"></i>
                  <p>Bookings</p>
                </a>
         </li>
         
           <li class="nav-item  {{ request()->routeIs('address.*') ? 'active' : '' }}">
           <a href="{{route('address.index')}}" class="nav-link">
                  <i class="nav-icon fa fa-address-card-o"></i>
                  <p>Address</p>
                </a>
         </li>
         
           <li class="nav-item  {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
           <a href="{{route('transactions.index')}}" class="nav-link">
                  <i class="nav-icon fa fa-exchange"></i>
                  <p>Transactions</p>
                </a>
         </li>
      
         
           <li class="nav-item  {{ request()->routeIs('pages') || request()->routeIs('pages.*') ? 'active' : '' }}">
           <a href="{{route('pages')}}" class="nav-link">
                  <i class="nav-icon fa fa-file"></i>
                  <p>Pages</p>
                </a>
         </li>
         
          <li class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">
                <a href="{{route('settings')}}" class="nav-link">
                  <i class="nav-icon fas fa-cog"></i>
                  <p>App Setting</p>
                </a>
         </li>
       
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
