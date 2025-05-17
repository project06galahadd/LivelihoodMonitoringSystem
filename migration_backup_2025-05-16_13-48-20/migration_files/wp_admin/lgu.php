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
            <h1>BARANGAY RECORD</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">HOME</a></li>
              <li class="breadcrumb-item active">BARANGAY</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
		  
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
		  
            <div class="card">
              <div class="card-header">
                      <h3 class="card-title"> 
              <a href="#add" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> REGISTER</a>
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
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Record #</th>
                      <th>Barangay Name</th>
                      <th>Barangay Captain</th>
                      <th>Contact Number</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql = "SELECT * FROM tbl_barangay WHERE STATUS != 'ARCHIVED' ORDER BY BRGY_NAME ASC";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                    ?>
                      <tr id="row_<?= $row['BRGY_ID']; ?>">
                        <td><?php echo $row['BRGY_ID']; ?></td>
                        <td><?php echo $row['BRGY_NAME']; ?></td>
                        <td><?php echo $row['BRGY_CAPTAIN']; ?></td>
                        <td><?php echo $row['BRGY_CONTACT']; ?></td>
                        <td>
                          <?php if($row['STATUS'] == 'ACTIVE'): ?>
                            <span class="badge badge-success">Active</span>
                          <?php else: ?>
                            <span class="badge badge-danger">Archived</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <button class="btn btn-primary btn-sm edit btn-flat" data-id="<?php echo $row['BRGY_ID']; ?>"><i class="fa fa-edit"></i> Edit</button>
                          <button class="btn btn-sm" onclick="triggerArchiveModal(this)"
                            data-brgyid="<?php echo $row['BRGY_ID']; ?>"
                            data-brgyname="<?php echo $row['BRGY_NAME']; ?>"
                            data-status="<?php echo $row['STATUS']; ?>"
                            style="background-color: <?php echo $row['STATUS'] == 'ARCHIVED' ? '#d9534f' : '#f0ad4e'; ?>; color: white;">
                            <i class="fa <?php echo $row['STATUS'] == 'ARCHIVED' ? 'fa-undo' : 'fa-archive'; ?>"></i>
                            <?php echo $row['STATUS'] == 'ARCHIVED' ? 'Unarchive' : 'Archive'; ?>
                          </button>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>
                  </tbody>
                </table>
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
  <?php include "includes/lgu_modal.php";?>
 <?php include "includes/footer.php";?>
<?php include "includes/archive_modal.php"; ?>

<script type="text/javascript">
function editLGU(self) {
      var brgyid = self.getAttribute("data-brgyid");
      var brgyname = self.getAttribute("data-brgyname");
      var brgycaptain = self.getAttribute("data-brgycaptain");
      var brgycontact = self.getAttribute("data-brgycontact");
      document.getElementById("EDIT_BRGY_ID").value = brgyid;
      document.getElementById("EDIT_BRGY_NAME").value = brgyname;
      document.getElementById("EDIT_BRGY_CAPTAIN").value = brgycaptain;
      document.getElementById("EDIT_BRGY_CONTACT").value = brgycontact;
      $("#lgu_edit_modal").modal("show");
    }

function triggerArchiveModal(btn) {
  var id = btn.getAttribute('data-brgyid');
  var name = btn.getAttribute('data-brgyname');
  var status = btn.getAttribute('data-status');
  openArchiveModal(id, 'barangay', name, status, window.location.pathname);
}
</script> 
</body>
</html>

