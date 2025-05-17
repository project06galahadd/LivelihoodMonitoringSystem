<?php
if (!isset($fullname)) {
    header('Location: signin.php');
    exit();
}
?>
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
        
        .nav-sidebar .nav-link {
            padding: 12px 15px;
        }
        
        .nav-sidebar .nav-link.active {
            background: #2c3e50;
            color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .nav-sidebar .nav-link.active i {
            color: #fff;
        }
        
        /* Add hover effects */
        .nav-sidebar .nav-item.hovered {
            background: rgba(255,255,255,0.05);
        }
    </style>

    <!-- Brand Logo -->
    <a href="home.php" class="brand-link">
        <img src="/LivelihoodMonitoringSystem/dist/img/Logo.png" alt="MSWD Logo" class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">MSWD Member</span>
    </a>

    <!-- Sidebar user panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex flex-column align-items-center">
        <div class="image">
            <img src="<?php echo !empty($user['profile_picture']) ? '../uploads/profile/' . $user['profile_picture'] : (!empty($user_data['profile_picture']) ? '../uploads/profile/' . $user_data['profile_picture'] : '/LivelihoodMonitoringSystem/dist/img/default-avatar.png'); ?>"
                class="img-circle elevation-2" alt="User Image" style="width:54px;height:54px;object-fit:cover;">
        </div>
        <div class="info">
            <a href="profile.php" class="d-block">
                <?php echo htmlspecialchars(isset($user['username']) ? $user['username'] : (isset($fullname) ? $fullname : 'Member')); ?>
            </a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
                <a href="home.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-home"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="livelihood_records.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'livelihood_records.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-chart-line"></i>
                    <p>Livelihood Records</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="household_records.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'household_records.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Household Records</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="news.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-newspaper"></i>
                    <p>News & Announcements</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="chat.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-comments"></i>
                    <p>Chat with Admin</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-user-cog"></i>
                    <p>Profile Settings</p>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Add sidebar script -->
<script src="../js/sidebar.js"></script>
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