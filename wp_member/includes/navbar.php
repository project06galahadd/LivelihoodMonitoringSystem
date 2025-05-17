<?php
if (!isset($fullname)) {
    header('Location: signin.php');
    exit();
}
?>

<!-- <div class="preloader flex-column justify-content-center align-items-center" style="background:none">
    <img class="animation__shakes" src="../images/loading-loader.gif" alt="AdminLTELogo" height="60" width="60">
  </div> -->

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-light" style="backdrop-filter: blur(8px); background: rgba(255,255,255,0.85); box-shadow: 0 4px 24px rgba(44,62,80,0.08); border-bottom: 1.5px solid #e3e6ec;">
    <style>
        .main-header {
            min-height: 64px;
            padding: 0 1.5rem;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: #2c3e50 !important;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .navbar-nav .nav-link {
            color: #2c3e50 !important;
            font-weight: 600;
            font-size: 1.08rem;
            transition: color 0.18s, background 0.18s;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #3498db !important;
            background: rgba(52, 152, 219, 0.08);
            border-radius: 8px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3498db;
            margin-right: 8px;
            background: #fff;
        }

        .dropdown-menu {
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.10);
            border: none;
            min-width: 180px;
        }

        .dropdown-item {
            font-weight: 600;
            font-size: 1.05rem;
            color: #2c3e50;
            transition: background 0.18s, color 0.18s;
        }

        .dropdown-item:hover {
            background: linear-gradient(90deg, #3498db 0%, #6dd5fa 100%);
            color: #fff;
        }

        @media (max-width: 991.98px) {
            .navbar-nav .nav-link {
                font-size: 1rem;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
            }
        }
    </style>
    <a class="navbar-brand d-flex align-items-center" href="home.php">
        <img src="/LivelihoodMonitoringSystem/dist/img/Logo.png" alt="Logo">
        <span><?= isset($SYS_NAME) && $SYS_NAME ? htmlspecialchars($SYS_NAME) : 'MSWD System'; ?></span>
    </a>
    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item">
            <a href="home.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">
                <i class="fas fa-home mr-1"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="livelihood.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'livelihood.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line mr-1"></i> Livelihood Records
            </a>
        </li>
        <li class="nav-item">
            <a href="household_case.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'household_case.php' ? 'active' : ''; ?>">
                <i class="fas fa-users mr-1"></i> Household Case Records
            </a>
        </li>
        <li class="nav-item">
            <a href="news.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : ''; ?>">
                <i class="fas fa-newspaper mr-1"></i> News & Announcements
            </a>
        </li>
        <li class="nav-item">
            <a href="chat.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'active' : ''; ?>">
                <i class="fas fa-comments mr-1"></i> Chat with Admin
            </a>
        </li>
        <li class="nav-item">
            <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-cog mr-1"></i> Profile Settings
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="<?php echo !empty($user['profile_picture']) ? '../uploads/profile/' . $user['profile_picture'] : (!empty($user_data['profile_picture']) ? '../uploads/profile/' . $user_data['profile_picture'] : '/LivelihoodMonitoringSystem/dist/img/default-avatar.png'); ?>" class="user-avatar mr-2" alt="User Avatar">
                <span class="d-none d-md-inline-block" style="font-weight:600; color:#2c3e50;">
                    <?php echo htmlspecialchars(isset($user['username']) ? $user['username'] : (isset($fullname) ? $fullname : 'Member')); ?>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="profile.php"><i class="fas fa-user mr-2"></i>Profile</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
            </div>
        </li>
    </ul>
</nav>
color: #fff;
}

@media (max-width: 991.98px) {
.navbar-nav .nav-link {
font-size: 1rem;
}

.user-avatar {
width: 32px;
height: 32px;
}
}
</style>
<a class="navbar-brand d-flex align-items-center" href="home.php">
    <img src="/LivelihoodMonitoringSystem/dist/img/Logo.png" alt="Logo">
    <span><?= isset($SYS_NAME) && $SYS_NAME ? htmlspecialchars($SYS_NAME) : 'MSWD System'; ?></span>
</a>
<ul class="navbar-nav ml-auto align-items-center">
    <li class="nav-item">
        <a href="home.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">
            <i class="fas fa-home mr-1"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a href="livelihood.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'livelihood.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line mr-1"></i> Livelihood Records
        </a>
    </li>
    <li class="nav-item">
        <a href="household_case.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'household_case.php' ? 'active' : ''; ?>">
            <i class="fas fa-users mr-1"></i> Household Case Records
        </a>
    </li>
    <li class="nav-item">
        <a href="news.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : ''; ?>">
            <i class="fas fa-newspaper mr-1"></i> News & Announcements
        </a>
    </li>
    <li class="nav-item">
        <a href="chat.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'active' : ''; ?>">
            <i class="fas fa-comments mr-1"></i> Chat with Admin
        </a>
    </li>
    <li class="nav-item">
        <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-cog mr-1"></i> Profile Settings
        </a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="<?php echo !empty($user['profile_picture']) ? '../uploads/profile/' . $user['profile_picture'] : (!empty($user_data['profile_picture']) ? '../uploads/profile/' . $user_data['profile_picture'] : '/LivelihoodMonitoringSystem/dist/img/default-avatar.png'); ?>" class="user-avatar mr-2" alt="User Avatar">
            <span class="d-none d-md-inline-block" style="font-weight:600; color:#2c3e50;">
                <?php echo htmlspecialchars(isset($user['username']) ? $user['username'] : (isset($fullname) ? $fullname : 'Member')); ?>
            </span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="profile.php"><i class="fas fa-user mr-2"></i>Profile</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
        </div>
    </li>
</ul>
</nav>