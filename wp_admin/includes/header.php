<?php 
//  header("Refresh: 5;url=age.php");
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
$CI	= "REC";	//Example only
$CIcnt = strlen($CI);
$offset	= $CIcnt + 6;

// Get the current month and year as two-digit strings 
$month = date("m"); // e.g. 09 
$year = date("y"); // e.g. 23  

// Get the last bill number from the database 
$query = "SELECT AUTO_NUMBER FROM tbl_autonumber ORDER BY AUTO_NUMBER DESC"; 
$result = mysqli_query($conn,$query); 
// Use mysqli_fetch_assoc() to get an associative array of the fetched row 
$row = mysqli_fetch_assoc($result); 
// Use $row['bilno'] to get the last bill number 
$lastid = $row['AUTO_NUMBER']; 	 

// Check if the last bill number is empty or has a different month or year
if(empty($lastid) || (substr($lastid, $CIcnt + 1, 2) != $month) || (substr($lastid, $CIcnt + 3, 2) != $year)) { 
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
    if($SYS_NAME==""){
    ?>
       <title>Not Set</title>
    <?php }else{ ?>
      <title><?=$SYS_EMAIL;?> | <?=$SYS_NAME;?></title>
    <?php }?>
  
    <?php 
    if($SYS_LOGO==""){
    ?>
      <link rel="icon" type="image/x-icon" href="../dist/img/Logo.png">
    <?php }else{ ?>
      <link rel="icon" type="image/x-icon" href="data:image/jpg;charset=utf8;base64,<?=base64_encode($SYS_LOGO); ?>">
    <?php }?>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- summernote -->
    <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="../plugins/jqvmap/jqvmap.min.css">
    <!-- Ekko Lightbox -->
    <link rel="stylesheet" href="../plugins/ekko-lightbox/ekko-lightbox.css">
    <!-- FullCalendar -->
    <link rel="stylesheet" href="../plugins/fullcalendar/lib/main.min.css">
    <!-- OverlayScrollbars -->
    <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <script>
    // Remove all loading screen JavaScript
    document.addEventListener('DOMContentLoaded', function() {
      // Handle chat navigation
      const chatLinks = document.querySelectorAll('.chat-link');
      chatLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          const href = this.getAttribute('data-href');
          if (href) {
            window.location.href = href;
          }
        });
      });
    });
  </script>

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
      <link rel="icon" type="image/x-icon" href="../dist/img/Logo.png">
    <?php }else{ ?>
      <link rel="icon" type="image/x-icon" href="data:image/jpg;charset=utf8;base64,<?=base64_encode($SYS_LOGO); ?>">
    <?php }?>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
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
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  
  
<!-- Class	Availability
.modal-fullscreen	Always
.modal-fullscreen-sm-down	Below 576px
.modal-fullscreen-md-down	Below 768px
.modal-fullscreen-lg-down	Below 992px
.modal-fullscreen-xl-down	Below 1200px
.modal-fullscreen-xxl-down	Below 1400px -->
</body>

