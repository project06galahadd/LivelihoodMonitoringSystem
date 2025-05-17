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
              <li class="breadcrumb-item active"> SETTINGS</li>
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
                <h3 class="card-title"><i class="text-primary fa fa-database"></i> DATABASE BACKUP</h3>
              </div>
              <div class="card-body">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Backup Options</label>
                    <div class="custom-control custom-radio">
                      <input type="radio" id="fullBackup" name="backupType" class="custom-control-input" value="full" checked>
                      <label class="custom-control-label" for="fullBackup">Full Backup (Database + Files)</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" id="dbBackup" name="backupType" class="custom-control-input" value="database">
                      <label class="custom-control-label" for="dbBackup">Database Only</label>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Backup Schedule</label>
                    <select class="form-control" id="backupSchedule">
                      <option value="manual">Manual Backup</option>
                      <option value="daily">Daily</option>
                      <option value="weekly">Weekly</option>
                      <option value="monthly">Monthly</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>Backup Retention</label>
                    <select class="form-control" id="backupRetention">
                      <option value="7">7 days</option>
                      <option value="14">14 days</option>
                      <option value="30">30 days</option>
                      <option value="90">90 days</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>Last Backup</label>
                    <p class="text-muted" id="lastBackupTime">No backup performed yet</p>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="button" class="btn btn-primary btn-sm" id="createBackup">
                  <i class="fa fa-download"></i> Create Backup
                </button>
                <button type="button" class="btn btn-info btn-sm" id="viewBackups">
                  <i class="fa fa-list"></i> View Backups
                </button>
              </div>
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

  <!-- Backup System Scripts -->
  <script>
    $(document).ready(function() {
      // Create Backup
      $('#createBackup').click(function() {
        const backupType = $('input[name="backupType"]:checked').val();
        const schedule = $('#backupSchedule').val();
        const retention = $('#backupRetention').val();

        $.ajax({
          url: 'backup_create.php',
          type: 'POST',
          data: {
            type: backupType,
            schedule: schedule,
            retention: retention
          },
          beforeSend: function() {
            $('#createBackup').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating Backup...');
          },
          success: function(response) {
            const data = JSON.parse(response);
            if(data.success) {
              toastr.success('Backup created successfully!');
              $('#lastBackupTime').text(new Date().toLocaleString());
            } else {
              toastr.error(data.message || 'Failed to create backup');
            }
          },
          error: function() {
            toastr.error('An error occurred while creating backup');
          },
          complete: function() {
            $('#createBackup').prop('disabled', false).html('<i class="fa fa-download"></i> Create Backup');
          }
        });
      });

      // View Backups
      $('#viewBackups').click(function() {
        $.ajax({
          url: 'backup_list.php',
          type: 'GET',
          success: function(response) {
            const data = JSON.parse(response);
            if(data.success) {
              // Show backups in a modal
              showBackupsModal(data.backups);
            } else {
              toastr.error(data.message || 'Failed to load backups');
            }
          },
          error: function() {
            toastr.error('An error occurred while loading backups');
          }
        });
      });

      function showBackupsModal(backups) {
        let html = '<div class="table-responsive"><table class="table table-bordered">';
        html += '<thead><tr><th>Date</th><th>Type</th><th>Size</th><th>Actions</th></tr></thead>';
        html += '<tbody>';

        backups.forEach(backup => {
          html += `<tr>
            <td>${backup.date}</td>
            <td>${backup.type}</td>
            <td>${backup.size}</td>
            <td>
              <button class="btn btn-sm btn-info" onclick="downloadBackup('${backup.id}')">
                <i class="fa fa-download"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="deleteBackup('${backup.id}')">
                <i class="fa fa-trash"></i>
              </button>
            </td>
          </tr>`;
        });

        html += '</tbody></table></div>';

        Swal.fire({
          title: 'Backup History',
          html: html,
          width: '800px',
          showCloseButton: true,
          showConfirmButton: false
        });
      }
    });

    function downloadBackup(id) {
      window.location.href = `backup_download.php?id=${id}`;
    }

    function deleteBackup(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "This backup will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: 'backup_delete.php',
            type: 'POST',
            data: { id: id },
            success: function(response) {
              const data = JSON.parse(response);
              if(data.success) {
                toastr.success('Backup deleted successfully');
                $('#viewBackups').click(); // Refresh the list
              } else {
                toastr.error(data.message || 'Failed to delete backup');
              }
            },
            error: function() {
              toastr.error('An error occurred while deleting backup');
            }
          });
        }
      });
    }
  </script>
</body>
</html>

