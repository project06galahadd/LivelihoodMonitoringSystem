<?php include "header.php";?>
<body>
  <div class="wrapper">
  <?php include "navbar.php";?>
	
	<div class="content container">
	  <section class="content-header">

    </section>
		<div class="row container">
    <?php include "sidebar.php"?>
        <div class="col-md-9">
            <div class="sticky-tops">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Please carefully review all fields in the online form and ensure that you provide complete and accurate information.</h4>
                  <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                  </button>
                  </div>
                </div>
                <form class="needs-validation"  autocomplete="off" enctype="multipart/form-data" novalidate>
                <div class="card-body text-uppercase">
                  <div class="row">
                   <div class="col-lg-12">
                   <h6 class="text-primary">PERSONAL INFORMATION</h6>
                   </div>
                    <div class="col-lg-4">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">First Name</label>
                      <input type="text"  class="form-control" id="FIRSTNAME" name="FIRSTNAME" placeholder="" required>
                    </div>
                    </div>
                    <div class="col-lg-4">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">Middle Name</label>
                      <input type="text"  class="form-control" id="MIDDLENAME" name="MIDDLENAME" placeholder="">
                    </div>
                    </div>
                    <div class="col-lg-4">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">Last Name</label>
                      <input type="text"  class="form-control" id="LASTNAME" name="LASTNAME" placeholder="" required>
                    </div>
                    </div>
                    <div class="col-lg-4">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">Gender</label>
                        <select class="form-control" id="GENDER" name="GENDER" required>
                          <option value=""></option>
                          <option>MALE</option>
                          <option>FEMALE</option>
                        </select>
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
                      <input type="text"  class="form-control" id="MOBILE" name="MOBILE" placeholder="" required>
                    </div>
                    </div>
                    <div class="col-lg-8">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">Barangay</label>
                      <select class="form-control select2" id="BARANGAY" name="BARANGAY" required>
                          <option value=""></option>
                            <?php
                              $stmt = $mysqli->prepare("SELECT * FROM tbl_barangay ORDER BY BRGY_NAME ASC");
                              if($stmt->execute()){
                                  $result = $stmt->get_result();
                                  if($result->num_rows>0){
                                      while($row = $result->fetch_assoc()){
                                        
                                         print '<option value='.$row['BRGY_NAME'].'>'.$row['BRGY_NAME'].'</option>';
                                      }
                                      
                                      $stmt->close();
                                  }
                              }
                            ?>
                        </select>
                    </div>
                    </div>

                    <div class="col-lg-12">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">Address</label>
                      <textarea type="text" rows="4" class="form-control" id="ADDRESS" name="ADDRESS" placeholder="" required></textarea>
                    </div>
                    </div>
                    <div class="col-lg-4">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">Educational Background</label>
                      <input type="text"  class="form-control" id="EDUCATIONAL_BACKGROUND" name="EDUCATIONAL_BACKGROUND" placeholder="" required>
                    </div>
                    </div>
                    <div class="col-lg-4">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">Employment History</label>
                      <input type="text"  class="form-control" id="EMPLOYMENT_HISTORY" name="EMPLOYMENT_HISTORY" placeholder="" required>
                    </div>
                    </div>
                    <div class="col-lg-4">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">Skills and Qualifications</label>
                      <input type="text"  class="form-control" id="SKILLS_QUALIFICATION" name="SKILLS_QUALIFICATION" placeholder="" required>
                    </div>
                    </div>

                    <div class="col-lg-12">
                    <h6 class="text-primary">APPLICATION INFORMATION</h6>
                    </div>
                    
                    <div class="col-lg-6">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">Please indicate your interest in the following livelihood program(s):</label>
                      <select class="form-control select2" id="DESIRED_LIVELIHOOD_PROGRAM" name="DESIRED_LIVELIHOOD_PROGRAM" required>
                          <option value=""></option>
                          <?php
                              $stmt = $mysqli->prepare("SELECT * FROM tbl_livelihood ORDER BY LIVELIHOOD_NAME ASC");
                              if($stmt->execute()){
                                  $result = $stmt->get_result();
                                  if($result->num_rows>0){
                                      while($row = $result->fetch_assoc()){
                                        
                                         print '<option value='.ucwords($row['LIVELIHOOD_NAME']).'>'.strtoupper(ucwords($row['LIVELIHOOD_NAME'])).'</option>';
                                      }
                                      
                                      $stmt->close();
                                  }
                              }
                            ?>
                        </select>
                    </div>
                    </div>
                    <div class="col-lg-6">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">How much experience do you have in the chosen livelihood program(s)?</label>
                      <select class="form-control select2" id="EXP_LIVELIHOOD_PROGRAM_CHOSEN" name="EXP_LIVELIHOOD_PROGRAM_CHOSEN" required>
                          <option value=""></option>
                          <option>BEGINNER</option>
                          <option>INTERMEDIATE</option>
                          <option>ADVANCED</option>
                        </select>
                    </div>
                    </div>
                    <div class="col-lg-6">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal"> Current Livelihood Situation</label>
                      <input type="text"  class="form-control" id="CURRENT_LIVELIHOOD_SITUATION" name="CURRENT_LIVELIHOOD_SITUATION" placeholder="" required>
                    </div>
                    </div>

                    <div class="col-lg-6">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">Are you willing to commit to the required training?</label>
                      <select class="form-control select2" id="REQUIRED_TRAINING" name="REQUIRED_TRAINING" required>
                          <option value=""></option>
                          <option>YES</option>
                          <option>NO</option>
                        </select>
                    </div>
                    </div>

                    <div class="col-lg-12">
                    <div class="form-group">
                      <label for="" class="control-label font-weight-normal">Please provide a brief reason why you are interested in participating in this livelihood program:</label>
                      <textarea type="text" rows="4" class="form-control" id="REASON_INTERESTED_IN_LIVELIHOOD" name="REASON_INTERESTED_IN_LIVELIHOOD" placeholder="" required></textarea>
                    </div>
                    </div>

                    <div class="col-lg-4">
                    <div class="form-group">
                    <label class="font-weight-normal">Lists of Valid </label>
                      <select class="form-control select2 text-uppercase" name="VALID_ID_NUMBER" id="VALID_ID_NUMBER" required>
                        <option value="" selected></option>
                        <option>Philippine Passport</option>
                        <option>Philippine Driver’s License </option>
                        <option>Professional RegulatoryCommission (PRC) Card </option>
                        <option>Postal ID</option>
                        <option>Armed Forces of thePhilippines ID</option>
                        <option>Social Security System(SSS)</option>
                        <option>Government ServiceInsurance System (GSIS) </option>
                        <option>Unified Multi-Purpose ID </option>
                        <option>Phil Health ID </option>
                        <option>Tax Identification Number(TIN) Card </option>
                        <option>Persons with disability(PWD) Card </option>
                        <option>National ID </option>
                      </select>
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                    <label class="font-weight-normal">Upload ID</label>
                    <input type="file" name="UPLOAD_ID" id="UPLOAD_ID" class="form-control" required>
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                    <label class="font-weight-normal">Upload ID with Selfie for verification</label>
                    <input type="file" name="UPLOAD_WITH_SELFIE" id="UPLOAD_WITH_SELFIE" class="form-control" required>
                    </div>
                  </div>

                  </div><!---/row-->
                </div>
                <div class="card-footer">
                  <button type="reset" class="btn btn-warning text-white">CLEAR</button>
                  <button type="submit" name="submit" class="btn btn-primary">SUBMIT</button>
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
  <?php include "scripts.php";?>
 