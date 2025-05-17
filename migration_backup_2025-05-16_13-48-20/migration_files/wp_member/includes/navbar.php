
  
  <!-- <div class="preloader flex-column justify-content-center align-items-center" style="background:none">
    <img class="animation__shakes" src="../images/loading-loader.gif" alt="AdminLTELogo" height="60" width="60">
  </div> -->

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark sticky-top" style="border:none;background:#0652DD">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
       <a href="#" class="nav-link"><?=$SYS_NAME;?></a>
      </li>
      <!---<li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>---->
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      
	  <div class="btn-group">
	<button type="button" class="btn bg-default">
      <span class="hidden-xs text-white"><?php echo $user['LASTNAME'].', '.$user['FIRSTNAME']; ?></span>
	</button>
	<button type="button" class="btn bg-default dropdown-toggle dropdown-icon text-white" data-toggle="dropdown">
	  <span class="sr-only">Toggle Dropdown</span>
	</button>
  <style>
    .hhhh a:hover{
      background:#0097e6;
    }
  </style>
	<div class="dropdown-menu hhhh" role="menu" style="border:none;background:#0652DD">
	  <!-- <a class="dropdown-item text-white" data-toggle="modal" href="#editProfile"> <i class="fa fa-user"></i> UPDATE INFORMATION</a>
	  <a class="dropdown-item text-white" data-toggle="modal" href="#profile"> <i class="fa fa-edit"></i> CHANGE PROFILE</a> -->
	  <a class="dropdown-item text-white" data-toggle="modal" href="#" data-target="#logout"><i class="fa fa-power-off"></i> SIGN OUT</a>
	</div>
  </div>
     <!---- <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>----->
    </ul>
  </nav>