<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Add sidebar styles -->
  <style>
    /* Add styles for hamburger menu */
    .fa-bars, .sidebar-toggle {
      cursor: pointer;
      pointer-events: auto !important;
      z-index: 1000;
    }
    
    .fa-bars:hover, .sidebar-toggle:hover {
      opacity: 0.8;
    }
    
    /* Ensure sidebar toggle is always clickable */
    .sidebar-toggle {
      position: relative;
      z-index: 1000;
    }
    
    .sidebar-dark-primary {
      background: linear-gradient(180deg, #1a1a1a 0%, #000000 100%);
    }
    
    .brand-link {
      border-bottom: 1px solid rgba(255,255,255,0.1);
      transition: all 0.3s ease;
    }
    
    .brand-link:hover {
      background: rgba(255,255,255,0.05);
    }
    
    .brand-image {
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      transition: transform 0.3s ease;
    }
    
    .brand-image:hover {
      transform: scale(1.05);
    }
    
    .user-panel {
      border-bottom: 1px solid rgba(255,255,255,0.1);
      padding: 15px;
    }
    
    .user-panel .image img {
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      transition: transform 0.3s ease;
    }
    
    .user-panel .image img:hover {
      transform: scale(1.1);
    }
    
    .nav-sidebar .nav-item {
      margin: 2px 0;
      position: relative;
      z-index: 1;
    }
    
    .nav-sidebar .nav-link {
      border-radius: 5px;
      margin: 0 8px;
      transition: all 0.3s ease;
      position: relative;
      z-index: 2;
      cursor: pointer;
      pointer-events: auto !important;
      color: rgba(255,255,255,0.8);
    }
    
    .nav-sidebar .nav-link:hover {
      background: rgba(255,255,255,0.1);
      transform: translateX(5px);
      color: #ffffff;
    }
    
    .nav-sidebar .nav-link.active {
      background: #2c3e50;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      color: #ffffff;
    }
    
    .nav-sidebar .nav-link p {
      font-weight: 500;
      transition: all 0.3s ease;
      margin: 0;
    }
    
    .nav-sidebar .nav-link:hover p {
      transform: translateX(5px);
    }
    
    .nav-sidebar .nav-treeview {
      display: none;
      opacity: 0;
      transform: translateY(-10px);
      transition: all 0.3s ease-in-out;
      background: rgba(0,0,0,0.2);
      border-left: 3px solid rgba(255,255,255,0.1);
      margin-left: 10px;
      padding: 5px 0;
    }
    
    .nav-sidebar .nav-item.menu-open > .nav-treeview {
      display: block;
      opacity: 1;
      transform: translateY(0);
    }
    
    .nav-sidebar .nav-treeview .nav-link {
      padding-left: 20px;
      opacity: 0.8;
      transition: all 0.3s ease;
    }
    
    .nav-sidebar .nav-treeview .nav-link:hover {
      opacity: 1;
      background: rgba(255,255,255,0.1);
      transform: translateX(3px);
    }
    
    .nav-sidebar .nav-icon {
      font-size: 1.1rem;
      margin-right: 10px;
      width: 20px;
      text-align: center;
      transition: transform 0.3s ease;
      color: rgba(255,255,255,0.7);
    }
    
    .nav-sidebar .nav-link:hover .nav-icon {
      color: #ffffff;
    }
    
    .nav-sidebar .nav-item.menu-open > .nav-link {
      background: rgba(255,255,255,0.1);
      color: #ffffff;
    }
    
    .nav-sidebar .nav-item.menu-open > .nav-link i.right {
      transition: transform 0.3s ease;
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
    }
    
    .nav-sidebar .nav-item.menu-open > .nav-link i.right {
      transform: translateY(-50%) rotate(-90deg);
    }
    
    .nav-sidebar .nav-link i.right {
      transition: transform 0.3s ease;
    }
    
    .nav-sidebar .nav-link {
      padding: 12px 15px;
    }
    
    .nav-sidebar .nav-treeview .nav-link {
      padding: 10px 15px;
    }
    
    .nav-sidebar .nav-item.menu-open > .nav-link {
      background: rgba(255,255,255,0.1);
      color: #fff;
    }
    
    .nav-sidebar .nav-link.active {
      background: #2c3e50;
      color: #fff;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .nav-sidebar .nav-link.active i {
      color: #fff;
    }

    /* Add styles for better interaction */
    .nav-sidebar .nav-item.has-treeview > .nav-link {
      position: relative;
    }

    .nav-sidebar .nav-item.has-treeview > .nav-link::after {
      display: none;
    }

    /* Improve submenu visibility */
    .nav-sidebar .nav-treeview {
      background: rgba(0,0,0,0.2);
      border-left: 3px solid rgba(255,255,255,0.1);
    }

    .nav-sidebar .nav-treeview .nav-link {
      opacity: 0.8;
      transition: all 0.3s ease;
    }

    .nav-sidebar .nav-treeview .nav-link:hover {
      opacity: 1;
      background: rgba(255,255,255,0.1);
    }

    /* Add animation for menu transitions */
    .nav-sidebar .nav-treeview {
      transition: all 0.3s ease;
    }

    .nav-sidebar .nav-item.menu-open > .nav-treeview {
      animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Fix for clickable areas */
    .nav-sidebar .nav-item,
    .nav-sidebar .nav-link,
    .nav-sidebar .nav-treeview {
      pointer-events: auto !important;
    }

    /* Ensure proper z-index stacking */
    .nav-sidebar .nav-item {
      position: relative;
      z-index: 1;
    }

    .nav-sidebar .nav-link {
      z-index: 2;
    }

    .nav-sidebar .nav-treeview {
      z-index: 3;
    }

    /* Fix for menu item spacing */
    .nav-sidebar .nav-item {
      margin: 4px 0;
    }

    .nav-sidebar .nav-treeview .nav-item {
      margin: 2px 0;
    }

    /* Improve submenu visibility */
    .nav-sidebar .nav-treeview {
      margin-left: 10px;
      border-left: 2px solid rgba(255,255,255,0.1);
    }

    /* Add hover effect for menu items */
    .nav-sidebar .nav-link:hover {
      background: rgba(255,255,255,0.1);
      transform: translateX(5px);
    }

    .nav-sidebar .nav-treeview .nav-link:hover {
      background: rgba(255,255,255,0.1);
      transform: translateX(3px);
    }
  </style>

  <!-- Add sidebar script -->
  <script src="../js/sidebar.js"></script>

  <!-- Brand Logo -->
  <a href="home.php" class="brand-link">
    <?php if ($SYS_LOGO == "") { ?>
      <img src="../dist/img/Logo.png" alt="SLP" class="brand-image img-circle elevation-3" style="opacity: .8">
    <?php } else { ?>
      <img src="data:image/jpg;charset=utf8;base64,<?= base64_encode($SYS_LOGO); ?>" class="brand-image img-circle elevation-3">
    <?php } ?>
    <span class="brand-text font-weight-light"><?= $SYS_EMAIL; ?></span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <?php if ($user['PROFILE'] == "") { ?>
          <img src="../dist/img/profile.jpg" class="img-circle elevation-2" alt="User Image">
        <?php } else { ?>
          <img src="data:image/jpg;charset=utf8;base64,<?= base64_encode($user['PROFILE']); ?>" class="img-circle elevation-2" alt="User Image">
        <?php } ?>
      </div>
      <div class="info">
        <a href="profile.php" class="d-block">
          <?= $user['LASTNAME'] . ', ' . $user['FIRSTNAME']; ?>
          <i class="fa fa-circle text-success right"></i>
        </a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <?php if ($user['ROLE'] == "ADMIN") { ?>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="home.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="notes.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'notes.php' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>Events & Announcements</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="chat.php" target="_self" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'active' : ''; ?>" onclick="window.location.href='chat.php'; return false;">
              <i class="nav-icon fas fa-comments"></i>
              <p>Chat</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="household_records.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'household_records.php' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-folder-open"></i>
              <p>Household Case Records</p>
            </a>
          </li>
          
          <li class="nav-item has-treeview <?= in_array(basename($_SERVER['PHP_SELF']), ['member.php', 'livelihood.php', 'lgu.php', 'prof_program.php']) ? 'menu-open' : ''; ?>">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-database"></i>
              <p>
                Monitoring Data
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="member.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'member.php' ? 'active' : ''; ?>">
                  <i class="nav-icon fas fa-users"></i>
                  <p>Beneficiaries</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="livelihood.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'livelihood.php' ? 'active' : ''; ?>">
                  <i class="nav-icon fas fa-briefcase"></i>
                  <p>Livelihood</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="lgu.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'lgu.php' ? 'active' : ''; ?>">
                  <i class="nav-icon fas fa-map-marker-alt"></i>
                  <p>Barangay</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="prof_program.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'prof_program.php' ? 'active' : ''; ?>">
                  <i class="nav-icon fas fa-file-alt"></i>
                  <p>Proof of Program</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item has-treeview <?= in_array(basename($_SERVER['PHP_SELF']), ['archive_members.php', 'archive_barangays.php']) ? 'menu-open' : ''; ?>">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-archive"></i>
              <p>
                Archives
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="archive_members.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'archive_members.php' ? 'active' : ''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Archived Members</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="archive_barangays.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'archive_barangays.php' ? 'active' : ''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Archived Barangays</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item has-treeview <?= in_array(basename($_SERVER['PHP_SELF']), ['chart_male_female.php', 'chart_senior_citizen.php', 'chart_rank_by_barangay.php', 'chart_livelihood_program.php']) ? 'menu-open' : ''; ?>">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>
                Analytics
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="chart_male_female.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'chart_male_female.php' ? 'active' : ''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Gender Distribution</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="chart_senior_citizen.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'chart_senior_citizen.php' ? 'active' : ''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Senior Citizens</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="chart_rank_by_barangay.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'chart_rank_by_barangay.php' ? 'active' : ''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Barangay Rankings</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="chart_livelihood_program.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'chart_livelihood_program.php' ? 'active' : ''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Program Statistics</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item">
            <a href="setting.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'setting.php' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-cog"></i>
              <p>Settings</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="logout.php" class="nav-link">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>
    <?php } ?>
  </div>
</aside>