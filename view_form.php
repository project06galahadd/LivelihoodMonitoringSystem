<?php include "header.php"; ?>

<body>
  <div class="wrapper">
    <?php include "navbar.php"; ?>

    <div class="content container mt-5">
      <section class="content-header">
      </section>
      <div class="container">
        <?php //include "sidebar.php"
        ?>
        <div class="col-md-12">
          <h4 class="text-white">SIGN IN</h4>
          <div class="sticky-tops">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">To view your record. Please ensure that you provide complete and accurate information.</h4>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <form class="view_form" autocomplete="off" enctype="multipart/form-data" novalidate>
                <div class="card-body text-uppercase">
                  <div class="row">
                    <div class="col-lg-12">
                      <h6 class="text-primary">PERSONAL INFORMATION</h6>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="" class="control-label font-weight-normal">First Name</label>
                        <input type="text" class="form-control" id="FIRSTNAME" name="FIRSTNAME" placeholder="" required>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="" class="control-label font-weight-normal">Middle Name</label>
                        <input type="text" class="form-control" id="MIDDLENAME" name="MIDDLENAME" placeholder="" required>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="" class="control-label font-weight-normal">Last Name</label>
                        <input type="text" class="form-control" id="LASTNAME" name="LASTNAME" placeholder="" required>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="" class="control-label font-weight-normal">Date of Birth</label>
                        <input type="date" id="DATE_OF_BIRTH" name="DATE_OF_BIRTH" class="form-control" required>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="" class="control-label font-weight-normal">Age</label>
                        <input type="text" class="form-control" id="AGE" name="AGE" readonly required>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="" class="control-label font-weight-normal">Mobile #</label>
                        <input type="text" class="form-control" id="MOBILE" name="MOBILE" placeholder="" required>
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="" class="control-label font-weight-normal">Barangay</label>
                        <select style="width:100%" class="form-control select2" id="BARANGAY" name="BARANGAY" required>
                          <option value=""></option>
                          <?php
                          $stmt = $mysqli->prepare("SELECT * FROM tbl_barangay ORDER BY BRGY_NAME ASC");
                          if ($stmt->execute()) {
                            $result = $stmt->get_result();
                            if ($result->num_rows > 0) {
                              while ($row = $result->fetch_assoc()) {

                                print '<option value=' . $row['BRGY_NAME'] . '>' . $row['BRGY_NAME'] . '</option>';
                              }

                              $stmt->close();
                            }
                          }
                          ?>
                        </select>
                      </div>
                    </div>




                  </div><!---/row-->
                </div>
                <div class="card-footer">
                  <button type="reset" class="btn btn-warning text-white">CLEAR</button>
                  <button type="submit" class="btn btn-primary">SUBMIT</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-wrapper -->
  </div><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include "scripts.php"; ?>