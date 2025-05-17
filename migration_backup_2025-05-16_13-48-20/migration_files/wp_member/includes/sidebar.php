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
    <?php if ($user['ROLE'] == "MEMBER") { ?>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="home.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="profile.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-user"></i>
              <p>My Profile</p>
            </a>
          </li>
          
          <li class="nav-item has-treeview <?= in_array(basename($_SERVER['PHP_SELF']), ['household_records.php', 'livelihood_records.php']) ? 'menu-open' : ''; ?>">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-folder-open"></i>
              <p>
                My Records
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="household_records.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'household_records.php' ? 'active' : ''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Household Case Records</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="livelihood_records.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'livelihood_records.php' ? 'active' : ''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Livelihood Records</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item">
            <a href="notifications.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-bell"></i>
              <p>Notifications</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="news.php" class="nav-link">
              <i class="nav-icon fas fa-newspaper"></i>
              <p>News & Announcements</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="chat.php" class="nav-link">
              <i class="nav-icon fas fa-comments"></i>
              <p>Chat</p>
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

<script>
// Add active class to parent menu items when child is active
document.addEventListener('DOMContentLoaded', function() {
  const activeLinks = document.querySelectorAll('.nav-link.active');
  activeLinks.forEach(link => {
    const parentItem = link.closest('.nav-treeview');
    if (parentItem) {
      const parentNavItem = parentItem.closest('.nav-item.has-treeview');
      if (parentNavItem) {
        parentNavItem.classList.add('menu-open');
      }
    }
  });
});
</script>
  