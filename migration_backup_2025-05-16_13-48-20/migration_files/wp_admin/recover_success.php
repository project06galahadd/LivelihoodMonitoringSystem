<?php
		session_start();
    include("includes/conn.php");
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
      <title><?=$SYS_EMAIL;?>| <?=$SYS_NAME;?></title>
    <?php }?>
  
  <?php 
    if($SYS_LOGO==""){
    ?>
      <link rel="icon" type="image/x-icon" href="../images/logo.png">
    <?php }else{ ?>
      <link rel="icon" type="image/x-icon" href="data:image/jpg;charset=utf8;base64,<?=base64_encode($SYS_LOGO); ?>">
    <?php }?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <meta http-equiv="refresh" content="10;url=recover_success_logout.php">
        <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
        <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/all.css">
        <link rel="stylesheet" href="../dist/css/adminlte.min.css">

		<link rel="stylesheet"href="https://site-assets.fontawesome.com/releases/v6.4.0/css/sharp-regular.css">
		<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/sharp-solid.css">
  <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    </head>
<body class="hold-transition login-page" style="background: rgba(1, 4, 136, 0.9);">
<div class="login-boxs col-md-5 mt-3" style="margin-top:0%">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
   <?php 
    if($SYS_LOGO==""){
    ?>
      <img class="brand-image" src="../images/logo.png">
    <?php }else{ ?>
      <img class="brand-image" width="100" src="data:image/jpg;charset=utf8;base64,<?=base64_encode($SYS_LOGO); ?>">
    <?php }?>
    </div>
    <div class="card-body text-center">
      <a href="#" class="h4">SUCCESSFULLY CHANGED</a>
      <p class="login-box-msgs">Your password has been successfully reset, Please wait.. you will redirect to login page. 
      Thank you for your patient!</p>
  </div>
  <div class="card-footer">
      
  </div>
</div>	
  <!-- /.card -->
<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
</body>
</html>

