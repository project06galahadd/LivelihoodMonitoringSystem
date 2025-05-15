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
            <h1>LIST OF REQUIREMENTS</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">HOME</a></li>
              <li class="breadcrumb-item active">REQUIREMENTS</li>
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
              <a href="#_add_modal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> REQUIREMENTS</a>
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
                <table id="example1" class="table table-bordered table-striped table-sm text-sm">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>REQUIREMENTS</th>
                    <th>ACTIONS</th>
                  </tr>
                  </thead>
                  <tbody>
				        	<?php
                    $sql = "SELECT * FROM tbl_requirements ORDER BY REQ_NAME ASC";
                    $query = $conn->query($sql);
				        	  $cnt=1;
                    while($row = $query->fetch_assoc()){
                      ?>
                        <tr>
                          <td><?=$cnt++; ?></td>
                          <td><?=$row['REQ_NAME']; ?></td>
                          <td align="right">
							            <div class="btn-group">
                            <button class="btn btn-primary btn-sm"
                            data-reqid="<?=$row['REQ_ID'];?>" 
                            data-reqname="<?=$row['REQ_NAME'];?>"
                            onclick="editLGU(this);" data-jario="tooltip" data-placement="top" title="EDIT"><i class="fa-solid fa fa-edit"></i> </button>
                            <button class="btn btn-danger btn-sm" 
                            data-reqid="<?=$row['REQ_ID'];?>" 
                            onclick="deleteLGU(this);" data-jario="tooltip" data-placement="top" title="DELETE"><i class="fa-solid fa fa-trash"></i> </button>
							            </div>
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
  <?php include "includes/requirements_modal.php";?>
 <?php include "includes/footer.php";?>

 <script type="text/javascript">
 function editLGU(self) {
      var reqid = self.getAttribute("data-reqid");
      var reqname = self.getAttribute("data-reqname");
      document.getElementById("edit_reqid").value = reqid;
      document.getElementById("edit_reqname").value = reqname;
      $("#edit_modal").modal("show");
    }

    function deleteLGU(self) {
      var reqid = self.getAttribute("data-reqid");
      document.getElementById("del_reqid").value = reqid;
      $("#del_modal").modal("show");
    }
</script> 
<script>
        $(document).ready(function () {

            $(document).on('click', '.remove-btn', function () {
                $(this).closest('.main-form').remove();
            });
            $(document).on('click', '.add-more-form', function () {
              
                $('.paste-new-forms').append('<div class="main-form">\
                <div class="row">\
                        <div class="col-sm-12">\
                    <label for="lastname" class="control-label font-weight-normal">REQUIREMENTS</label>\
                       <div class="input-group">\
                            <input type="text" class="form-control" name="REQ_NAME[]" required>\
                              <div class="input-group-prepend">\
                                <button type="button" class="btn btn-danger remove-btn"><i class="fa fa-times"></i></button>\
                            </div>\
                        </div>\
                    </div>\
                    </div>\
              </div>');
            });

        });
    </script>

</body>
</html>

