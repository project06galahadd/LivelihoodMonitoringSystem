<?php
session_start();
require_once "../wp_admin/includes/conn.php";

// Check if user is logged in and has member role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'MEMBER') {
    header('Location: signin.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$fullname = $_SESSION['fullname'];

// Get user profile data
try {
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} catch (Exception $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    $user = null;
}

// Handle profile update
if(isset($_POST['update'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    
    // Check if email is already taken by another user
    $check_email = $conn->prepare("SELECT * FROM tbl_users WHERE email = ? AND user_id != ?");
    $check_email->bind_param("si", $email, $user_id);
    $check_email->execute();
    $email_result = $check_email->get_result();
    
    if($email_result->num_rows > 0){
        $_SESSION['error'] = 'Email already taken';
    } else {
        $update = $conn->prepare("UPDATE tbl_users SET firstname = ?, lastname = ?, email = ? WHERE user_id = ?");
        $update->bind_param("sssi", $firstname, $lastname, $email, $user_id);
        
        if($update->execute()){
            $_SESSION['success'] = 'Profile updated successfully';
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
        } else {
            $_SESSION['error'] = 'Error updating profile';
        }
    }
    
    header('location: profile.php');
    exit();
}

// Handle password change
if(isset($_POST['change_password'])){
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if($new_password != $confirm_password){
        $_SESSION['error'] = 'New passwords do not match';
    } else {
        if(password_verify($current_password, $user['password'])){
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE tbl_users SET password = ? WHERE user_id = ?");
            $update->bind_param("si", $hashed_password, $user_id);
            
            if($update->execute()){
                $_SESSION['success'] = 'Password changed successfully';
            } else {
                $_SESSION['error'] = 'Error changing password';
            }
        } else {
            $_SESSION['error'] = 'Current password is incorrect';
        }
    }
    
    header('location: profile.php');
    exit();
}

// Get member information
$query = "SELECT m.*, u.email, u.username 
          FROM tbl_members m 
          JOIN tbl_users u ON m.user_id = u.user_id 
          WHERE m.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();

// Get enrolled programs
$query = "SELECT p.*, ep.enrollment_date, ep.status as enrollment_status 
          FROM tbl_enrolled_programs ep 
          JOIN tbl_programs p ON ep.program_id = p.program_id 
          WHERE ep.user_id = ? 
          ORDER BY ep.enrollment_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$enrolled_programs = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Livelihood Monitoring System</title>
    <link rel="icon" type="image/png" href="/LivelihoodMonitoringSystem/dist/img/Logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
        }
        body { font-family: 'Source Sans Pro', sans-serif; background: var(--light-bg); position: relative; overflow-x: hidden; }
        .main-sidebar { background: var(--primary-color); box-shadow: 2px 0 10px rgba(0,0,0,0.1); transition: all 0.3s ease; }
        .brand-link { border-bottom: 1px solid rgba(255,255,255,0.1); color: #fff !important; background: var(--primary-color); padding: 1rem; }
        .brand-link img { width: 40px; height: 40px; margin-right: 10px; }
        .user-panel { border-bottom: 1px solid rgba(255,255,255,0.1); padding: 1.5rem 1rem; background: rgba(255,255,255,0.05); }
        .user-panel .image img { width: 60px; height: 60px; border: 3px solid rgba(255,255,255,0.2); }
        .user-panel .info a { color: #fff; font-weight: 600; font-size: 1.1rem; }
        .user-panel .info small { color: rgba(255,255,255,0.7); }
        .nav-sidebar .nav-item { margin: 5px 10px; }
        .nav-sidebar .nav-link { color: rgba(255,255,255,0.8); border-radius: 8px; padding: 12px 15px; transition: all 0.3s ease; display: flex; align-items: center; }
        .nav-sidebar .nav-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .nav-sidebar .nav-link.active { background: var(--secondary-color); color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .nav-sidebar .nav-link i { margin-right: 10px; width: 20px; text-align: center; }
        .main-header { background: #fff; border-bottom: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .main-header .nav-link { color: var(--text-color) !important; }
        .dropdown-menu { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px; }
        .dropdown-item { padding: 10px 20px; transition: all 0.3s ease; }
        .dropdown-item:hover { background: var(--secondary-color); color: #fff; }
        .content-wrapper { background: var(--light-bg); }
        .content-header { padding: 1.5rem 1rem; background: transparent; }
        .content-header h1 { color: var(--text-color); font-weight: 600; font-size: 1.8rem; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 1.5rem; background: #fff; }
        .card-header { background: #fff; border-bottom: 1px solid #eee; padding: 1rem 1.5rem; border-radius: 10px 10px 0 0; }
        .card-title { color: var(--text-color); font-weight: 600; margin: 0; }
        .form-group { margin-bottom: 1.5rem; }
        .form-control { border-radius: 8px; border: 1px solid #ddd; padding: 0.75rem 1rem; transition: all 0.3s ease; }
        .form-control:focus { border-color: var(--secondary-color); box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25); }
        .btn { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; }
        .btn-primary { background: var(--secondary-color); border: none; }
        .btn-primary:hover { background: #2980b9; transform: translateY(-1px); }
        .alert { border: none; border-radius: 8px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; }
        .alert-success { background: var(--success-color); color: #fff; }
        .alert-danger { background: var(--danger-color); color: #fff; }
        .profile-image { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 1.5rem; border: 5px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        @media (max-width: 768px) { .main-sidebar { transform: translateX(-100%); } .sidebar-open .main-sidebar { transform: translateX(0); } .content-wrapper { margin-left: 0; } }
        .profile-header {
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .profile-card {
            transition: transform 0.3s;
        }
        .profile-card:hover {
            transform: translateY(-5px);
        }
        .program-badge {
            font-size: 0.8rem;
            padding: 0.5rem;
            margin: 0.2rem;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Video Background -->
    <video autoplay muted loop id="myVideo" class="video-background">
        <source src="/LivelihoodMonitoringSystem/dist/video/background.mp4" type="video/mp4">
    </video>

    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                        <i class="fas fa-user-circle mr-2"></i><?php echo htmlspecialchars($fullname); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="home.php" class="brand-link">
                <img src="/LivelihoodMonitoringSystem/dist/img/Logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">MSWD</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="home.php" class="nav-link">
                                <i class="nav-icon fas fa-home"></i>
                                <p>Home</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="household_case.php" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Household Case Records</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="livelihood.php" class="nav-link">
                                <i class="nav-icon fas fa-briefcase"></i>
                                <p>Livelihood Programs</p>
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
                                <p>Chat with Admin</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Profile Settings</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <?php
                    if(isset($_SESSION['error'])){
                        echo "
                        <div class='alert alert-danger alert-dismissible'>
                            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                            <h4><i class='icon fa fa-warning'></i> Error!</h4>
                            ".$_SESSION['error']."
                        </div>
                        ";
                        unset($_SESSION['error']);
                    }
                    if(isset($_SESSION['success'])){
                        echo "
                        <div class='alert alert-success alert-dismissible'>
                            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                            <h4><i class='icon fa fa-check'></i> Success!</h4>
                            ".$_SESSION['success']."
                        </div>
                        ";
                        unset($_SESSION['success']);
                    }
                    ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Profile Information</h3>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <img src="<?php echo !empty($user['profile_picture']) ? '../uploads/profile/' . $user['profile_picture'] : '/LivelihoodMonitoringSystem/dist/img/default-avatar.png'; ?>" 
                                             class="profile-image" alt="Profile Picture">
                                    </div>
                                    <form method="POST" action="">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" class="form-control" name="firstname" value="<?php echo $user['firstname']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control" name="lastname" value="<?php echo $user['lastname']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>" required>
                                        </div>
                                        <button type="submit" name="update" class="btn btn-primary">
                                            <i class="fas fa-save mr-2"></i>Update Profile
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Change Password</h3>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <div class="form-group">
                                            <label>Current Password</label>
                                            <input type="password" class="form-control" name="current_password" required>
                                        </div>
                                        <div class="form-group">
                                            <label>New Password</label>
                                            <input type="password" class="form-control" name="new_password" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm New Password</label>
                                            <input type="password" class="form-control" name="confirm_password" required>
                                        </div>
                                        <button type="submit" name="change_password" class="btn btn-primary">
                                            <i class="fas fa-key mr-2"></i>Change Password
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
    <script>
        $(function() {
            // Initialize any additional JavaScript functionality here
        });
    </script>
</body>
</html> 