<?php
error_reporting(0);
include "wp_admin/includes/conn.php";
$mysqli = new mysqli('localhost', 'root', '', 'livelihood_database');
//  $page = basename($_SERVER['PHP_SELF']);//gets current URL
// if ($page == "nonaccesspage.php") //any page u don't want to be accessed directly
// header('Location:index.php');
// else if($page == "nonaccesspage2.php") //page 2 which is not accessible 
// header('Location:index.php');
$checkedHindi = "";
$checkedEng = "";
$sql_query = "SELECT * FROM tbl_system_setting";
$sql_query_run = $conn->query($sql_query);
if ($sql_query_run->num_rows > 0) {
  foreach ($sql_query_run as $key => $value_setting) {
    $SYS_ID = $value_setting['SYS_ID'];
    $SYS_NAME = $value_setting['SYS_NAME'];
    $SYS_ADDRESS = $value_setting['SYS_ADDRESS'];
    $SYS_LOGO = $value_setting['SYS_LOGO'];
    $SYS_EMAIL = $value_setting['SYS_EMAIL'];
    $SYS_ABOUT = $value_setting['SYS_ABOUT'];
    $SYS_ISDEFAULT = $value_setting['SYS_ISDEFAULT'];
    $SYS_SECOND_LOGO = $value_setting['SYS_SECOND_LOGO'];

    if ($SYS_ISDEFAULT == "YES") {
      $checkedEng = 'checked';
    } elseif ($SYS_ISDEFAULT == "NO") {
      $checkedHindi = 'checked';
    }
  }
} else {
  $SYS_ID = "";
  $SYS_NAME = "";
  $SYS_ADDRESS = "";
  $SYS_LOGO = "";
  $SYS_EMAIL = "";
  $SYS_ABOUT = "";
  $SYS_ISDEFAULT = "";
  $checkedHindi = "";
  $checkedEng = "";
}



$CI  = "REC";  //Example only
$CIcnt = strlen($CI);
$offset  = $CIcnt + 6;

// Get the current month and year as two-digit strings 
$month = date("m"); // e.g. 09 
$year = date("y"); // e.g. 23  

// Get the last bill number from the database 
$query = "SELECT AUTO_NUMBER FROM tbl_autonumber ORDER BY AUTO_NUMBER DESC";
$result = mysqli_query($conn, $query);
// Use mysqli_fetch_assoc() to get an associative array of the fetched row 
$row = mysqli_fetch_assoc($result);
// Use $row[‘bilno’] to get the last bill number 
$lastid = $row['AUTO_NUMBER'];

// Check if the last bill number is empty or has a different month or year
if (empty($lastid) || (substr($lastid, $CIcnt + 1, 2) != $month) || (substr($lastid, $CIcnt + 3, 2) != $year)) {
  // Start a new sequence with 0001 
  $number = "$CI-$month$year-0001";
} else {
  // Increment the last four digits by one 
  $idd = substr($lastid, $offset); // e.g. 0001 
  $id = str_pad($idd + 1, 4, 0, STR_PAD_LEFT); // e.g. 0002 
  $number = "$CI-$month$year-$id";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
  if ($SYS_NAME == "") {
  ?>
    <title>MSWD | LivelihoodMonitoringSystem</title>
  <?php } else { ?>
    <title><?= $SYS_EMAIL; ?> | <?= $SYS_NAME; ?></title>
  <?php } ?>

  <?php
  if ($SYS_LOGO == "") {
  ?>
    <link rel="icon" type="image/x-icon" href="../images/logo.png">
  <?php } else { ?>
    <link rel="icon" type="image/x-icon" href="data:image/jpg;charset=utf8;base64,<?= base64_encode($SYS_LOGO); ?>">
  <?php } ?>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

  <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">

  <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="dist/css/style.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <link rel="stylesheet" href="plugins/fullcalendar/main.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/all.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/sharp-thin.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/sharp-solid.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/sharp-regular.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/sharp-light.css">

  <style>
    .navbar a {
      transition: 0.3s ease;

      border-top: 4px solid transparent;
      border-bottom: 4px solid transparent;
    }

    .navbar a:hover {
      /* border-top: 4px solid white; */
      border-bottom: 2px solid yellow;

    }
  </style>
</head>