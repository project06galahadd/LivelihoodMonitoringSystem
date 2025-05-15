<?php 
date_default_timezone_set('Asia/Manila');
require_once "includes/session.php";
$CURRENT_DATE =date('Y-m-d');
$checkedHindi ="";
$checkedEng="";
$sql_query ="SELECT * FROM tbl_system_setting";
$sql_query_run =$conn->query($sql_query);
if($sql_query_run->num_rows >0){
    foreach ($sql_query_run as $key => $value_setting) {
          $SYS_ID =$value_setting['SYS_ID'];
          $SYS_NAME=$value_setting['SYS_NAME'];
          $SYS_ADDRESS=$value_setting['SYS_ADDRESS'];
          $SYS_LOGO=$value_setting['SYS_LOGO'];
          $SYS_EMAIL=$value_setting['SYS_EMAIL'];
          $SYS_ABOUT=$value_setting['SYS_ABOUT'];
          $SYS_ISDEFAULT=$value_setting['SYS_ISDEFAULT'];
          $SYS_SECOND_LOGO=$value_setting['SYS_SECOND_LOGO'];
         
        if($SYS_ISDEFAULT == "YES") {
            $checkedEng = 'checked';
        } elseif($SYS_ISDEFAULT == "NO") {
            $checkedHindi = 'checked';
        }
    }
      
}else{
      $SYS_ID ="";
      $SYS_NAME="";
      $SYS_ADDRESS="";
      $SYS_LOGO="";
      $SYS_EMAIL="";
      $SYS_ABOUT="";
      $SYS_ISDEFAULT="";
      $checkedHindi ="";
      $checkedEng="";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
 
	<?php 
    if($SYS_NAME==""){
    ?>
       <title>Not Set</title>
    <?php }else{ ?>
      <title><?=$SYS_EMAIL;?> | <?=$SYS_NAME;?></title>
    <?php }?>
  
  <?php 
    if($SYS_LOGO==""){
    ?>
      <link rel="icon" type="image/x-icon" href="../images/logo.png">
    <?php }else{ ?>
      <link rel="icon" type="image/x-icon" href="data:image/jpg;charset=utf8;base64,<?=base64_encode($SYS_LOGO); ?>">
    <?php }?>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

<link rel="stylesheet" href="../plugins/toastr/toastr.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../plugins/jqvmap/jqvmap.min.css">
  <link rel="stylesheet" href="../plugins/ekko-lightbox/ekko-lightbox.css">
    <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/fullcalendar/lib/main.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  
  <!-- Daterange picker -->

  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="../dist/css/fullscreen_modal.css">
    <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  
  
<!-- Class	Availability
.modal-fullscreen	Always
.modal-fullscreen-sm-down	Below 576px
.modal-fullscreen-md-down	Below 768px
.modal-fullscreen-lg-down	Below 992px
.modal-fullscreen-xl-down	Below 1200px
.modal-fullscreen-xxl-down	Below 1400px -->

  <style>
    /* Loading Screen Styles */
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0);
      opacity: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      backdrop-filter: blur(0px);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      pointer-events: none;
    }

    .loading-overlay.active {
      opacity: 1;
      background: rgba(255, 255, 255, 0.92);
      backdrop-filter: blur(8px);
      pointer-events: all;
    }

    .loading-content {
      text-align: center;
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      transform: translateY(20px);
      opacity: 0;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .loading-overlay.active .loading-content {
      transform: translateY(0);
      opacity: 1;
    }

    .loading-logo {
      width: 140px;
      height: 140px;
      filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
      animation: smoothPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes smoothPulse {
      0% { transform: scale(1); }
      50% { transform: scale(0.97); }
      100% { transform: scale(1); }
    }

    .loading-text {
      margin-top: 20px;
      color: #2c3e50;
      font-size: 18px;
      font-weight: 500;
      letter-spacing: 0.5px;
      opacity: 0;
      transform: translateY(10px);
      animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards 0.2s;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Optimize performance */
    .loading-overlay * {
      will-change: transform, opacity;
      backface-visibility: hidden;
      -webkit-font-smoothing: antialiased;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <!-- Loading Screen -->
  <div class="loading-overlay">
    <div class="loading-content">
      <img src="../dist/img/Logo.png" alt="MSWD Logo" class="loading-logo">
      <div class="loading-text">Loading...</div>
    </div>
  </div>

  <!-- Add loading screen JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const loadingOverlay = document.querySelector('.loading-overlay');
      let loadingTimeout;
      
      // Show loading screen with minimum duration
      function showLoading() {
        clearTimeout(loadingTimeout);
        requestAnimationFrame(() => {
          loadingOverlay.classList.add('active');
          document.body.style.overflow = 'hidden';
        });
      }

      // Hide loading screen with minimum duration
      function hideLoading() {
        clearTimeout(loadingTimeout);
        loadingTimeout = setTimeout(() => {
          requestAnimationFrame(() => {
            loadingOverlay.classList.remove('active');
            document.body.style.overflow = '';
          });
        }, 400); // Minimum loading time to prevent flashing
      }

      // Handle all navigation link clicks
      document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && !link.hasAttribute('download') && !link.getAttribute('href').startsWith('#')) {
          showLoading();
        }
      });

      // Handle form submissions
      document.addEventListener('submit', function(e) {
        if (e.target.tagName === 'FORM') {
          showLoading();
        }
      });

      // Hide loading screen when page is fully loaded
      window.addEventListener('load', hideLoading);

      // Hide loading screen when navigating back/forward
      window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
          hideLoading();
        }
      });

      // Add loading screen for AJAX requests
      let activeRequests = 0;
      
      const originalXHR = window.XMLHttpRequest;
      function newXHR() {
        const xhr = new originalXHR();
        xhr.addEventListener('loadstart', function() {
          activeRequests++;
          showLoading();
        });
        xhr.addEventListener('loadend', function() {
          activeRequests--;
          if (activeRequests === 0) {
            hideLoading();
          }
        });
        return xhr;
      }
      window.XMLHttpRequest = newXHR;

      // Handle fetch requests
      const originalFetch = window.fetch;
      window.fetch = function() {
        showLoading();
        return originalFetch.apply(this, arguments)
          .then(function(response) {
            hideLoading();
            return response;
          })
          .catch(function(error) {
            hideLoading();
            throw error;
          });
      };

      // Preload animation
      window.addEventListener('DOMContentLoaded', () => {
        requestAnimationFrame(() => {
          loadingOverlay.style.visibility = 'visible';
        });
      });
    });
  </script>

