<?php
include 'includes/session.php';
include 'includes/header.php';
?>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Household Case Records</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active">Household Case Records</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <?php
          if (isset($_SESSION['success'])) {
            echo "
              <div class='alert alert-success alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4><i class='icon fa fa-check'></i> Success!</h4>
                ".$_SESSION['success']."
              </div>
            ";
            unset($_SESSION['success']);
          }
          if (isset($_SESSION['error'])) {
            echo "
              <div class='alert alert-danger alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4><i class='icon fa fa-warning'></i> Error!</h4>
                ".$_SESSION['error']."
              </div>
            ";
            unset($_SESSION['error']);
          }
          ?>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">List of Household Case Records</h3>
                </div>
                <div class="card-body">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Date Submitted</th>
                        <th>Beneficiary Name</th>
                        <th>Interviewed By</th>
                        <th>Address</th>
                        <th>Problem Presented</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT h.*, 
                             CONCAT(m.FIRSTNAME, ' ', m.LASTNAME) as member_name 
                             FROM tbl_household_case_records h 
                             LEFT JOIN tbl_members m ON h.submitted_by = m.MEMID 
                             ORDER BY h.date_created DESC";
                      $query = $conn->query($sql);
                      while($row = $query->fetch_assoc()){
                        $status_class = '';
                        switch($row['status']) {
                          case 'PENDING':
                            $status_class = 'warning';
                            break;
                          case 'APPROVED':
                            $status_class = 'success';
                            break;
                          case 'REJECTED':
                            $status_class = 'danger';
                            break;
                        }
                        ?>
                        <tr>
                          <td><?php echo date('M d, Y', strtotime($row['date_created'])); ?></td>
                          <td><?php echo $row['beneficiary_lastname'].', '.$row['beneficiary_firstname'].' '.$row['beneficiary_middlename']; ?></td>
                          <td><?php echo $row['interviewed_by_lastname'].', '.$row['interviewed_by_firstname'].' '.$row['interviewed_by_middlename']; ?></td>
                          <td><?php echo $row['complete_address']; ?></td>
                          <td><?php echo $row['problem_presented']; ?></td>
                          <td><span class="badge badge-<?php echo $status_class; ?>"><?php echo $row['status']; ?></span></td>
                          <td>
                            <button class="btn btn-info btn-sm view-record" data-id="<?php echo $row['id']; ?>">
                              <i class="fas fa-eye"></i> View
                            </button>
                            <?php if($row['status'] == 'PENDING'): ?>
                            <button class="btn btn-success btn-sm approve-record" data-id="<?php echo $row['id']; ?>">
                              <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn btn-danger btn-sm reject-record" data-id="<?php echo $row['id']; ?>">
                              <i class="fas fa-times"></i> Reject
                            </button>
                            <?php endif; ?>
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

    <?php include 'includes/footer.php'; ?>
  </div>

  <!-- View Record Modal -->
  <div class="modal fade" id="viewRecordModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Household Case Record Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="record-details"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Approve Record Modal -->
  <div class="modal fade" id="approveRecordModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Approve Record</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="household_case_records_approve.php" method="post">
          <div class="modal-body">
            <input type="hidden" name="id" id="approve_id">
            <div class="form-group">
              <label>Remarks (Optional)</label>
              <textarea class="form-control" name="remarks" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success" name="approve">Approve</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Reject Record Modal -->
  <div class="modal fade" id="rejectRecordModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Reject Record</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="household_case_records_reject.php" method="post">
          <div class="modal-body">
            <input type="hidden" name="id" id="reject_id">
            <div class="form-group">
              <label>Reason for Rejection</label>
              <textarea class="form-control" name="remarks" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger" name="reject">Reject</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include 'includes/scripts.php'; ?>
  <script>
  $(function(){
    $('#example1').DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $('.view-record').click(function(e){
      e.preventDefault();
      var id = $(this).data('id');
      $.ajax({
        type: 'POST',
        url: 'household_case_records_view.php',
        data: {id:id},
        success: function(response){
          $('#record-details').html(response);
          $('#viewRecordModal').modal('show');
        }
      });
    });

    $('.approve-record').click(function(e){
      e.preventDefault();
      var id = $(this).data('id');
      $('#approve_id').val(id);
      $('#approveRecordModal').modal('show');
    });

    $('.reject-record').click(function(e){
      e.preventDefault();
      var id = $(this).data('id');
      $('#reject_id').val(id);
      $('#rejectRecordModal').modal('show');
    });
  });
  </script>
</body>
</html> 