<?php @include "includes/header.php";?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- .navbar -->
  <?php @include "includes/navbar.php";?>
  <!-- /.navbar -->
  <?php @include "includes/sidebar.php";?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>SETTINGS</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"> HOME</li>
              <li class="breadcrumb-item active"> SETTIINGS</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      <?php
            if(isset($_SESSION['error'])){
              echo "
              <div id='alert' class='alert alert-danger' id='alert'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4><i class='icon fa fa-warning'></i> ERROR!</h4>
                ".$_SESSION['error']."
              </div>
              ";
              unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])){
              echo "
              <div id='alert' class='alert alert-success' id='alert'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4><i class='icon fa fa-check'></i> SUCCESS!</h4>
                ".$_SESSION['success']."
              </div>
              ";
              unset($_SESSION['success']);
            }
              ?>
        <div class="row">
		 <!-- /.col (left) -->
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><span class="fa fa-image"></span> LOGO</h3>
              </div>
              <div class="card-body">
                <form action="setting_logo.php?return=<?=basename($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" id="form-logo-update">
                <input type="hidden" class="form-control" value="<?=$SYS_ID;?>" name="SYS_ID" required>
                <div class="col-md-12">
                    <div class="form-group">
                      <label for="">PRIMARY LOGO</label>
                        <input class="form-control" name="SYS_LOGO" type="file" id="settingformFile" onchange="Settingspreview()"><br>
                        <center>
                        <?php
                          if($SYS_LOGO==""){
                        ?>
                         <img id="settingframe" src="../dist/img/Logo.png" class="img-fluid " style="border-radius:10px">
                        <?php }else{ ?>
                         <img id="settingframe" src="data:image/jpg;charset=utf8;base64,<?=base64_encode($SYS_LOGO); ?>" class="img-fluid" style="border-radius:10px;">
                          <?php } ?>
                          </center>
                    </div>
                   </div>
                </form>
              </div>
            <div class="card-footer">
            <button type="submit" form="form-logo-update" name="form-logo-update" class="btn bg-teal btn-sm"> <i class="fa fa-light fa-pen-to-square"></i> UPDATE</button>
            </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="text-red fa fa-regular fa-location-dot fa-fade"></i> LOCATION MAP</h3>
              </div>
              <div class="card-body">
				          <div class="col-md-12">
                  <div class="embed-responsive embed-responsive-16by9">
					  <iframe class="embed-responsive-item" src="https://maps.google.com/maps?q=<?=$SYS_ADDRESS;?>
					&t=&z=13&ie=UTF8&iwloc=&output=embed" allowfullscreen></iframe>
					</div>   
                   </div>
              </div>
            <div class="card-footer">
         
            </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->             

          </div>
		  
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
              <h3 class="card-title"> 
                  <i class="fa fa-solid fa-memo-circle-info"></i> Set Default
              </h3>
			      	<div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
              <form class="form-horizontal" method="POST" action="setting_edit.php?return=<?=basename($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data" id="form-system-update">
          		  <div class="row">
                    <div class="col-sm-6">
                       <div class="form-group">
                             <label for="lastname" class="control-label">NAME</label>
                            <input type="hidden" class="form-control" value="<?=$SYS_ID;?>" name="SYS_ID" required>
                            <input type="text" class="form-control" value="<?=$SYS_NAME;?>" name="SYS_NAME" placeholder="NAME" required>
                        </div>
                    </div>
                    
                    <div class="col-sm-6">
                       <div class="form-group">
                             <label for="lastname" class="control-label">SHORT NAME</label>
                            <input type="text" class="form-control" value="<?=$SYS_EMAIL;?>" name="SYS_EMAIL" placeholder ="SHORT NAME" required>
                        </div>
                    </div>
                    <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">ADDRESS</label>
                            <input type="text" class="form-control" value="<?=$SYS_ADDRESS;?>" name="SYS_ADDRESS" placeholder ="ADDRESS" required>
                        </div>
                    </div>
                    <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">ABOUT US</label>
                            <textarea rows="8" class="form-control summernote" name="SYS_ABOUT" placeholder ="ABOUT US" required><?=$SYS_ABOUT;?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                    <label class="control-label">SET AS TO DEFAULT</label>
                    <div class="form-group clearfix">
                      <div class="icheck-primary d-inline">
                        <input type="radio" id="radioPrimary1" name="r1" value="YES" <?=$checkedEng;?>/>
                        <label for="radioPrimary1">
                        YES
                        </label>
                      </div>
                      <div class="icheck-danger d-inline">
                        <input type="radio" id="radioPrimary2" name="r1" value="NO" <?=$checkedHindi;?>/>
                        <label for="radioPrimary2">
                        NO
                        </label>
                      </div>
                      
                    </div>
                  </div>

                </div><!----row-->
               </form>

              </div>
              <div class="card-footer border-success">
                <button type="submit" form="form-system-update" name="form-system-update" class="btn bg-teal btn-sm"> <i class="fa fa-light fa-pen-to-square"></i> UPDATE</button>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 <?php include "includes/footer.php";?>

 <script>
      function Settingspreview() {
          settingframe.src = URL.createObjectURL(event.target.files[0]);
      }
      function settingclearImage() {
          document.getElementById('settingformFile').value = null;
          settingframe.src = "";
      }
  </script>
   <script>
      function secondSettingspreview() {
        secondsettingframe.src = URL.createObjectURL(event.target.files[0]);
      }
      function settingclearImage() {
          document.getElementById('secondsettingformFile').value = null;
          secondsettingframe.src = "";
      }
  </script>
</body>
</html>

