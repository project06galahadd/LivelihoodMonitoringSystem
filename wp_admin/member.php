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
              <h1>MEMBERS RECORD</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">HOME</a></li>
                <li class="breadcrumb-item active">MEMBERS</li>
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
                    <a href="#add_member" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fa fa-user-plus"></i> REGISTER</a>
                    <a href="export/xls.php?xls=members" class="btn btn-primary btn-sm"><i class="fa fa-file-excel"></i> EXPORT</a>
                    <a href="chat.php" class="btn btn-info btn-sm"><i class="fa fa-comments"></i> CHAT</a>
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
                        <th>RECORD NO</th>
                        <th>MEMBER NAME</th>
                        <th>SEX</th>
                        <th>DOB[AGE]</th>
                        <th>APPLICATION DATE</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT * FROM tbl_members ORDER BY LASTNAME ASC";
                      $query = $conn->query($sql);
                      $cnt = 1;
                      while ($row = $query->fetch_assoc()) {
                        $dob_rows = $row['DATE_OF_BIRTH'];
                        $dob = new DateTime($dob_rows);
                        $today   = new DateTime('today');
                        $year = $dob->diff($today)->y;
                        $month = $dob->diff($today)->m;
                        $day = $dob->diff($today)->d;
                        // echo "Age is"." ".$year."year"." ",$month."months"." ".$day."days <br>";

                        if ($today >= $dob) {
                          $sqlage = "UPDATE tbl_members SET AGE='$year' WHERE MEMID='" . $row['MEMID'] . "'";
                          $conn->query($sqlage);
                        }
                      ?>
                        <tr id="row_<?= $row['MEMID']; ?>">
                          <td><?= $cnt++; ?></td>
                          <td><?= $row['RECORD_NUMBER']; ?></td>
                          <td><?= $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME']; ?></td>
                          <td><?= $row['GENDER']; ?></td>
                          <td><?= $row['DATE_OF_BIRTH']; ?> [<?= $row['AGE']; ?>]</td>
                          <td><?= $row['DATE_OF_APPLICATION']; ?></td>
                          <td>
                            <a href="#" data-status="<?= $row['STATUS']; ?>" data-memid="<?= $row['MEMID']; ?>" data-membername="<?= $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME']; ?>" data-memremarks="<?= $row['STATUS_REMARKS']; ?>" onclick="actionsMember(this);" data-jario="tooltip" data-placement="top" title="PLEASE TAKE ACTIONS">
                              <?php
                              if ($row['STATUS'] == "PENDING") {
                                echo '<span class="text-warning">PENDING</span>';
                              } elseif ($row['STATUS'] == "APPROVED") {
                                echo '<span class="text-primary">APPROVED</span>';
                              } elseif ($row['STATUS'] == "DEACTIVE") {
                                echo '<span class="text-danger">DEACTIVE</span>';
                              } elseif ($row['STATUS'] == "REJECTED") {
                                echo '<span class="text-danger">REJECTED</span>';
                              } elseif ($row['STATUS'] == "ARCHIVED") {
                                echo '<span class="text-secondary">ARCHIVED</span>';
                              } elseif ($row['STATUS'] == "ACTIVE") {
                                echo '<span class="text-success">ACTIVE</span>';
                              }
                              ?>
                            </a>
                          </td>
                          <td align="right">
                            <div class="btn-group">
                              <a href="chat.php?member=<?= $row['MEMID']; ?>" class="btn btn-success btn-sm" data-jario="tooltip" data-placement="top" title="CHAT">
                                <i class="fa fa-comments"></i>
                              </a>
                              <a href="member_info.php?member_info=<?= $row['MEMID']; ?>&record=<?= $row['RECORD_NUMBER']; ?>&name=<?= str_replace(' ', '_', $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME']); ?>" class="btn btn-info btn-sm" data-jario="tooltip" data-placement="top" title="DETAILS">
                                <i class="fa-solid fa fa-user-check"></i>
                              </a>
                              <button class="btn btn-primary btn-sm"
                                data-memid="<?= $row['MEMID']; ?>"
                                data-fname="<?= $row['FIRSTNAME']; ?>"
                                data-mname="<?= $row['MIDDLENAME']; ?>"
                                data-lname="<?= $row['LASTNAME']; ?>"
                                data-gender="<?= $row['GENDER']; ?>"
                                data-dob="<?= $row['DATE_OF_BIRTH']; ?>"
                                data-age="<?= $row['AGE']; ?>"
                                data-mobile="<?= $row['MOBILE']; ?>"
                                data-barangay="<?= $row['BARANGAY']; ?>"
                                data-address="<?= $row['ADDRESS']; ?>"
                                data-education="<?= $row['EDUCATIONAL_BACKGROUND']; ?>"
                                data-employment="<?= $row['EMPLOYMENT_HISTORY']; ?>"
                                data-skills="<?= $row['SKILLS_QUALIFICATION']; ?>"
                                data-desired="<?= $row['DESIRED_LIVELIHOOD_PROGRAM']; ?>"
                                data-programchosen="<?= $row['EXP_LIVELIHOOD_PROGRAM_CHOSEN']; ?>"
                                data-currentliveli="<?= $row['CURRENT_LIVELIHOOD_SITUATION']; ?>"
                                data-reqtraining="<?= $row['REQUIRED_TRAINING']; ?>"
                                data-reasoninterest="<?= $row['REASON_INTERESTED_IN_LIVELIHOOD']; ?>"
                                onclick="editMember(this);" data-jario="tooltip" data-placement="top" title="EDIT">
                                <i class="fa-solid fa fa-edit"></i>
                              </button>
                              <button class="btn btn-warning btn-sm" onclick="triggerArchiveModal(this)"
                                data-memid="<?= $row['MEMID']; ?>"
                                data-membername="<?= $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME']; ?>"
                                data-status="<?= $row['STATUS']; ?>"
                                style="background-color: <?= $row['STATUS'] == 'ARCHIVED' ? '#d9534f' : '#f0ad4e'; ?>; color: white;">
                                <i class="fa <?= $row['STATUS'] == 'ARCHIVED' ? 'fa-undo' : 'fa-archive'; ?>"></i>
                                <?= $row['STATUS'] == 'ARCHIVED' ? 'Unarchive' : 'Archive'; ?>
                              </button>
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
    <?php include "includes/member_modal.php"; ?>
    <?php include "includes/footer.php"; ?>
    <?php @include "includes/archive_modal.php"; ?>

    <script type="text/javascript">
      function editMember(self) {
        var memid = self.getAttribute("data-memid");
        var fname = self.getAttribute("data-fname");
        var mname = self.getAttribute("data-mname");
        var lname = self.getAttribute("data-lname");
        var gender = self.getAttribute("data-gender");
        var dob = self.getAttribute("data-dob");
        var age = self.getAttribute("data-age");
        var mobile = self.getAttribute("data-mobile");
        var barangay = self.getAttribute("data-barangay");
        var address = self.getAttribute("data-address");
        var education = self.getAttribute("data-education");
        var employment = self.getAttribute("data-employment");
        var skills = self.getAttribute("data-skills");
        var desired = self.getAttribute("data-desired");
        var programchosen = self.getAttribute("data-programchosen");
        var currentliveli = self.getAttribute("data-currentliveli");
        var reqtraining = self.getAttribute("data-reqtraining");
        var reasoninterest = self.getAttribute("data-reasoninterest");
        document.getElementById("EDIT_MEMID").value = memid;
        document.getElementById("EDIT_FNAME").value = fname;
        document.getElementById("EDIT_MNAME").value = mname;
        document.getElementById("EDIT_LNAME").value = lname;
        document.getElementById("EDIT_GENDER").innerHTML = gender;
        document.getElementById("EDIT_DOB").value = dob;
        document.getElementById("EDIT_AGE").value = age;
        document.getElementById("EDIT_MOBILE").value = mobile;
        document.getElementById("EDIT_BARANGAY").value = barangay;
        document.getElementById("EDIT_ADDRESS").value = address;
        document.getElementById("EDIT_EDUCATION").value = education;
        document.getElementById("EDIT_EMPLOYMENT").value = employment;
        document.getElementById("EDIT_SKILLS").value = skills;
        document.getElementById("EDIT_DESIRE").innerHTML = desired;
        document.getElementById("EDIT_PROGRAMCHOSEN").innerHTML = programchosen;
        document.getElementById("EDIT_CURRENTLIVELI").value = currentliveli;
        document.getElementById("EDIT_REQTRAINING").innerHTML = reqtraining;
        document.getElementById("EDIT_REASONINTEREST").value = reasoninterest;
        $("#member_edit_modal").modal("show");
      }

      function deleteMember(self) {
        var memid = self.getAttribute("data-memid");
        var membername = self.getAttribute("data-membername");
        document.getElementById("del_memid").value = memid;
        document.getElementById("del_membername").innerHTML = membername;
        $("#member_del_modal").modal("show");
      }

      function triggerArchiveModal(btn) {
        var memid = btn.getAttribute('data-memid');
        var name = btn.getAttribute('data-membername');
        var status = btn.getAttribute('data-status');
        openArchiveModal(memid, 'member', name, status, window.location.pathname);
      }

      function actionsMember(self) {
        var memid = self.getAttribute("data-memid");
        var membername = self.getAttribute("data-membername");
        var memstatus = self.getAttribute("data-status");
        var memremarks = self.getAttribute("data-memremarks");
        document.getElementById("actions_memid").value = memid;
        document.getElementById("actions_membername").innerHTML = membername;
        document.getElementById("actions_membstatus").innerHTML = memstatus;
        document.getElementById("actions_memremarks").innerHTML = memremarks;
        $("#member_actions_modal").modal("show");
      }
    </script>

  </div>
</body>

</html>