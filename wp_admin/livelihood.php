<?php @include "includes/header.php"; ?>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- .navbar -->
    <?php @include "includes/navbar.php"; ?>
    <!-- /.navbar -->
    <?php @include "includes/sidebar.php"; ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>LIVELIHOOD PROGRAMS</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">HOME</a></li>
                <li class="breadcrumb-item active">LIVELIHOOD</li>
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
                    <a href="#add" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add New</a>
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
                <div class="card-body">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Program Name</th>
                        <th>Description</th>
                        <th>Date Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT * FROM tbl_livelihood ORDER BY LIVELIHOOD_NAME ASC";
                      $query = $conn->query($sql);
                      while($row = $query->fetch_assoc()){
                      ?>
                        <tr id="row_<?= $row['LIVELIHOOD_ID']; ?>">
                          <td><?= $row['LIVELIHOOD_ID']; ?></td>
                          <td><?= $row['LIVELIHOOD_NAME']; ?></td>
                          <td><?= $row['LIVELIHOOD_DESCRIPTION']; ?></td>
                          <td><?= date('M d, Y', strtotime($row['LIVELIHOOD_CREATED'])); ?></td>
                          <td>
                            <span class="badge badge-success">Active</span>
                          </td>
                          <td>
                            <button class="btn btn-primary btn-sm edit btn-flat" 
                              data-id="<?= $row['LIVELIHOOD_ID']; ?>"
                              data-name="<?= $row['LIVELIHOOD_NAME']; ?>"
                              data-description="<?= $row['LIVELIHOOD_DESCRIPTION']; ?>">
                              <i class="fa fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteLivelihood(this)"
                              data-id="<?= $row['LIVELIHOOD_ID']; ?>"
                              data-name="<?= $row['LIVELIHOOD_NAME']; ?>">
                              <i class="fa fa-trash"></i> Remove
                            </button>
                          </td>
                        </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
    <?php include "includes/livelihood_modal.php"; ?>
    <?php include "includes/footer.php"; ?>
    <?php include "includes/archive_modal.php"; ?>

    <script type="text/javascript">
      function editLivelihood(self) {
        var id = self.getAttribute("data-id");
        var name = self.getAttribute("data-name");
        var description = self.getAttribute("data-description");
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_description").value = description;
        $("#editModal").modal("show");
      }

      function deleteLivelihood(btn) {
        if (confirm('Are you sure you want to remove this livelihood program?')) {
          var id = btn.getAttribute('data-id');
          var name = btn.getAttribute('data-name');
          
          // Create a form and submit it
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = 'livelihood_delete.php';
          
          var idInput = document.createElement('input');
          idInput.type = 'hidden';
          idInput.name = 'id';
          idInput.value = id;
          
          var nameInput = document.createElement('input');
          nameInput.type = 'hidden';
          nameInput.name = 'name';
          nameInput.value = name;
          
          form.appendChild(idInput);
          form.appendChild(nameInput);
          document.body.appendChild(form);
          form.submit();
        }
      }

      function triggerArchiveModal(btn) {
        var id = btn.getAttribute('data-id');
        var type = btn.getAttribute('data-type');
        var name = btn.getAttribute('data-name');
        var status = btn.getAttribute('data-status');
        openArchiveModal(id, type, name, status, window.location.pathname);
      }
    </script>
</body>
</html>