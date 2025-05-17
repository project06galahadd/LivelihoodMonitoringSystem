<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <style>
    .sidebar-dark-primary {
      background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
    }
    
    .brand-link {
      border-bottom: 1px solid rgba(255,255,255,0.1);
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
    }
    
    .nav-sidebar .nav-link {
      border-radius: 5px;
      margin: 0 8px;
      transition: all 0.3s ease;
    }
    
    .nav-sidebar .nav-link:hover {
      background: rgba(255,255,255,0.1);
      transform: translateX(5px);
    }
    
    .nav-sidebar .nav-link.active {
      background: #3498db;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .nav-sidebar .nav-link p {
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .nav-sidebar .nav-link:hover p {
      transform: translateX(5px);
    }
    
    .nav-sidebar .nav-treeview {
      background: rgba(0,0,0,0.1);
      border-radius: 5px;
      margin: 5px 0;
      padding: 5px 0;
    }
    
    .nav-sidebar .nav-treeview .nav-link {
      margin: 2px 8px;
    }
    
    .nav-sidebar .nav-treeview .nav-link p {
      font-size: 13px;
    }
    
    .nav-sidebar .nav-icon {
      font-size: 1.1rem;
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }
    
    .nav-sidebar .nav-item.menu-open > .nav-link {
      background: rgba(255,255,255,0.1);
    }
    
    .nav-sidebar .nav-item.menu-open > .nav-link i.right {
      transform: rotate(-90deg);
    }
  </style>

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