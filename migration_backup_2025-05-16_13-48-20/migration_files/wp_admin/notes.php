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
              <h1>ANNOUNCEMENTS</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">HOME</a></li>
                <li class="breadcrumb-item active">ANNOUNCEMENTS</li>
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
              if (isset($_SESSION['error'])) {
                echo "
                <div id='alert' class='alert alert-danger' id='alert'>
                  <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                  <h4><i class='icon fa fa-warning'></i> ERROR!</h4>
                  " . $_SESSION['error'] . "
                </div>
                ";
                unset($_SESSION['error']);
              }
              if (isset($_SESSION['success'])) {
                echo "
                <div id='alert' class='alert alert-success' id='alert'>
                  <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                  <h4><i class='icon fa fa-check'></i> SUCCESS!</h4>
                  " . $_SESSION['success'] . "
                </div>
                ";
                unset($_SESSION['success']);
              }
              ?>

              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <a href="#add" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> NEW</a>
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
                        <th>TITLE</th>
                        <th>DESCRIPTION </th>
                        <th>START TIME</th>
                        <th>ENDT TIME</th>
                        <th>ACTIONS</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT * FROM schedule_list ORDER BY start_datetime ASC";
                      $query = $conn->query($sql);
                      $cnt = 1;
                      while ($row = $query->fetch_assoc()) {
                      ?>
                        <tr id="row_<?= $row['id']; ?>">
                          <td><?= $cnt; ?></td>
                          <td><?= $row['title']; ?></td>
                          <td><?= $row['description']; ?></td>
                          <td><?= date('F d, Y h:i:s A', strtotime($row['start_datetime'])); ?></td>
                          <td><?= date('F d, Y h:i:s A', strtotime($row['end_datetime'])); ?></td>
                          <td align="right">
                            <div class="btn-group">
                              <button data-id="<?= $row['id']; ?>" class="btn bg-success btn-sm edit"><i class="fa-solid fa fa-edit"></i> </button>
                              <button data-id="<?= $row['id']; ?>" class="btn btn-sm" onclick="triggerArchiveModal(this)"
                                data-title="<?= $row['title']; ?>"
                                data-status="<?= $row['STATUS'] ?? 'ACTIVE'; ?>"
                                style="background-color: <?= (isset($row['STATUS']) && $row['STATUS'] == 'ARCHIVED') ? '#d9534f' : '#f0ad4e'; ?>; color: white;">
                                <i class="fa <?= (isset($row['STATUS']) && $row['STATUS'] == 'ARCHIVED') ? 'fa-undo' : 'fa-archive'; ?>"></i>
                                <?= (isset($row['STATUS']) && $row['STATUS'] == 'ARCHIVED') ? 'Unarchive' : 'Archive'; ?>
                              </button>
                            </div>
                          </td>
                        </tr>
                      <?php
                        $cnt++;
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
    <?php include "includes/notes_modal.php"; ?>
    <?php include "includes/footer.php"; ?>
    <?php include "includes/archive_modal.php"; ?>

    <script>
      $(function() {
        $('body').on('click', '.edit', function(e) {
          e.preventDefault();
          $('#edit').modal('show');
          var id = $(this).data('id');
          getRow(id);
        });

        $('body').on('click', '.delete', function(e) {
          e.preventDefault();
          $('#delete').modal('show');
          var id = $(this).data('id');
          getRow(id);
        });

        $('body').on('click', '.info', function(e) {
          e.preventDefault();
          $('#infoarticle').modal('show');
          var id = $(this).data('id');
          getRow(id);
        });


      });

      function getRow(id) {
        $.ajax({
          type: 'POST',
          url: 'notes_row.php',
          data: {
            id: id
          },
          dataType: 'json',
          success: function(response) {
            $('.id').val(response.id);
            $('.del_title').html(response.title);
            $('.del_description').html(response.description);
            $('.del_date').html(response.start_datetime);
            $('.del_time').html(response.end_datetime);

            $('#edit_title').val(response.title);
            $('#edit_description').val(response.description);
            $('#edit_date').val(response.start_datetime);
            $('#edit_time').val(response.end_datetime);

          }
        });
      }

      function triggerArchiveModal(btn) {
        var id = btn.getAttribute('data-id');
        var name = btn.getAttribute('data-title');
        var status = btn.getAttribute('data-status');
        openArchiveModal(id, 'announcement', name, status, window.location.pathname);
      }
    </script>
</body>

</html>