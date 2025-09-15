<div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="{{asset('/storage/icons/logo.jpg')}}" alt="AdminLTELogo" height="60" width="60" style="border-radius: 50%">
    </div>

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
        <!-- Online Status Indicator -->
        <li class="nav-item">
          <span class="nav-link">
            <i class="fas fa-circle text-success" id="online-status-icon" title="Online"></i>
            <small id="online-users-count" class="d-none">0</small>
          </span>
        </li>
        
        <!-- Navbar Search -->
        {{-- <li class="nav-item">
          <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="fas fa-search"></i>
          </a>
          <div class="navbar-search-block">
            <form class="form-inline">
              <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                  <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                  <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </li> --}}        <!-- Messages Dropdown Menu -->


        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fa fa-user"></i> Hello!,   {{ Auth::user()->name }}  
          </a>
        </li>


        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fa fa-power-off"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <div class="dropdown-divider"></div>
            <a href="{{route('manager.password.edit')}}" class="dropdown-item mt-2">
              <i class="fa fa-lock"></i> Change Password
            </a>
            @auth
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <a class="dropdown-item p-3 text-dark"  href=""
                      onclick="event.preventDefault();
                                 this.closest('form').submit();">
                  <i class="fa fa-arrow-right"></i> Log Out
              </a>
          </form>
          @endauth
            
        </li>


      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
        <a class="navbar-brand" href="#brand">
          <img src="{{asset('/storage/icons/logo.jpg')}}"  class="logo" alt="logo" style="width:50px; height:50px;">
          <span class="brand-text font-weight-light">Isaac Fruit N' Vegetable</span>
        </a>
      <!--a href="" class="brand-link">
        <span class="brand-text font-weight-light">Isaac Fruit N' Vegetable</span>
      </a-->

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->

        <!-- SidebarSearch Form -->


        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-item">
              <a href="{{route('manager')}}" class="nav-link{{ request()->routeIs('manager') ? ' active' : '' }}">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>
                  Dashboard
                </p>
              </a>
            </li>



            <li class="nav-item">
              <a href="{{route('manager.products')}}" class="nav-link{{ request()->routeIs('manager.products') ? ' active' : '' }}">
                  <i class="nav-icon fas fa-warehouse"></i>
                <p>
                  Inventory
                </p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{route('manager.rejected-goods.index')}}" class="nav-link{{ request()->routeIs('manager.rejected-goods.*') ? ' active' : '' }}">
                <i class="nav-icon fas fa-times-circle"></i>
                <p>
                  Rejected Goods
                </p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{route('manager.discrepancy-report.index')}}" class="nav-link{{ request()->routeIs('manager.discrepancy-report.*') ? ' active' : '' }}">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>
                  Discrepancy Report
                </p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{route('manager.brands')}}" class="nav-link {{ request()->routeIs('manager.brands') ? ' active' : '' }}">
                <i class="nav-icon fas fa-handshake"></i>
                <p>
                   Brand and Branches
                </p>
              </a>
            </li>

             <li class="nav-item">
              <a href="{{route('manager.orders')}}" class="nav-link {{ request()->routeIs('manager.orders') ? ' active' : '' }}">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>
                   Order Creation
                </p>
              </a>
            </li>

             <li class="nav-item">
              <a href="{{ route('manager.past-orders.index') }}" class="nav-link {{ request()->routeIs('manager.past-orders.*') ? ' active' : '' }}">
                <i class="nav-icon fas fa-history"></i>
                <p>
                   Past Orders
                </p>
              </a>
            </li>


            <li class="nav-header">Maintenance</li>


            <li class="nav-item">
              <a href="{{route('manager.managers')}}" class="nav-link {{ request()->routeIs('manager.managers') ? ' active' : '' }}">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>
                   Manage Accounts
                </p>
              </a>
            </li>
            
            <li class="nav-item">
              <a href="" class="nav-link">
                <i class="nav-icon fa fa-scroll"></i>
                <p>
                   for edit
                </p>
              </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="nav-icon fa fa-users-cog"></i>
                  <p>
                     for edit
                  </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="nav-icon fa fa-desktop"></i>
                  <p>
                     for edit
                  </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="nav-icon fa fa-cogs"></i>
                  <p>
                     for edit
                  </p>
                </a>
            </li>



          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>
