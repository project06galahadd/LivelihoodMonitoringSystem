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
$fullname = $_SESSION['fullname'];

// Get user profile data
$user_data = null;
try {
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
} catch (Exception $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    $user_data = null;
}

// Fetch news and announcements
try {
    $stmt = $conn->prepare("
        SELECT na.*, u.fullname as posted_by_name 
        FROM news_announcements na 
        JOIN tbl_users u ON na.posted_by = u.user_id 
        WHERE na.status = 'active' 
        ORDER BY na.date_posted DESC
    ");
    $stmt->execute();
    $news_result = $stmt->get_result();
} catch (Exception $e) {
    error_log("Error fetching news: " . $e->getMessage());
    $news_result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Announcements | MSWD</title>
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
        .breadcrumb { background: transparent; padding: 0; }
        .breadcrumb-item a { color: var(--secondary-color); }
        .breadcrumb-item.active { color: var(--text-color); }
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); background: #fff; }
        .card-header { background: #fff; border-bottom: 1px solid #eee; border-radius: 10px 10px 0 0; }
        .card-title { color: var(--text-color); font-weight: 600; }
        .news-card { transition: transform 0.3s ease; }
        .news-card:hover { transform: translateY(-5px); }
        .news-image { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem; }
        .news-date { color: #6c757d; font-size: 0.9rem; }
        .news-author { color: var(--secondary-color); font-weight: 500; }
        .news-content { color: var(--text-color); line-height: 1.6; }
        .news-badge { position: absolute; top: 1rem; right: 1rem; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .news-badge.important { background: var(--accent-color); color: #fff; }
        .news-badge.announcement { background: var(--secondary-color); color: #fff; }
        @media (max-width: 768px) { .main-sidebar { transform: translateX(-100%); } .sidebar-open .main-sidebar { transform: translateX(0); } .content-wrapper { margin-left: 0; } }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Video Background -->
    <video autoplay muted loop id="myVideo" class="video-background">
        <source src="/LivelihoodMonitoringSystem/dist/video/background.mp4" type="video/mp4">
    </video>

    <div class="wrapper">
        <?php include "includes/navbar.php"; ?>
        <?php include "includes/sidebar.php"; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">News & Announcements</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <?php
                        if ($news_result && $news_result->num_rows > 0) {
                            while ($news = $news_result->fetch_assoc()) {
                                $badge_class = $news['type'] === 'important' ? 'important' : 'announcement';
                                ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card news-card">
                                        <?php if ($news['image']) { ?>
                                            <img src="<?php echo htmlspecialchars($news['image']); ?>" class="news-image" alt="News Image">
                                        <?php } ?>
                                        <div class="card-body">
                                            <span class="news-badge <?php echo $badge_class; ?>">
                                                <?php echo ucfirst($news['type']); ?>
                                            </span>
                                            <h5 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h5>
                                            <p class="news-date">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                <?php echo date('F d, Y', strtotime($news['date_posted'])); ?>
                                            </p>
                                            <p class="news-author">
                                                <i class="far fa-user mr-1"></i>
                                                Posted by: <?php echo htmlspecialchars($news['posted_by_name']); ?>
                                            </p>
                                            <p class="news-content">
                                                <?php echo nl2br(htmlspecialchars($news['content'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="col-12"><div class="alert alert-info">No news or announcements available at the moment.</div></div>';
                        }
                        ?>
                    </div>
                </div>
            </section>
        </div>
        <?php include "includes/footer.php"; ?>
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