<!---FOR ADD---->
<div class="modal fade" id="add_member">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><span class="fa fa-plus"></span> REGISTER MEMBER</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal needs-validation" method="POST" action="member_add.php" enctype="multipart/form-data" novalidate>
        <div class="modal-body text-uppercase">
          <div class="row">
            <div class="col-lg-12">
              <h6 class="text-primary">PERSONAL INFORMATION</h6>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <input type="hidden" class="form-control" name="AUTO_NUMBER" value="<?= $number; ?>" required>
                <label for="" class="control-label font-weight-normal">First Name</label>
                <input type="text" class="form-control" name="FIRSTNAME" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Middle Name</label>
                <input type="text" class="form-control" name="MIDDLENAME" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Last Name</label>
                <input type="text" class="form-control" name="LASTNAME" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Gender</label>
                <select style="width:100%" class="form-control" name="GENDER" required>
                  <option value=""></option>
                  <option>MALE</option>
                  <option>FEMALE</option>
                </select>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Date of Birth</label>
                <input type="date" id="EDIT_DOB" name="DATE_OF_BIRTH" class="form-control" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Age</label>
                <input type="text" id="EDIT_AGE" class="form-control" name="AGE" readonly required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Mobile #</label>
                <input type="text" class="form-control" name="MOBILE" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-8">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Barangay</label>
                <select style="width:100%" class="form-control select2" name="BARANGAY" required>
                  <option value=""></option>
                  <?php
                  $stmt = $conn->prepare("SELECT * FROM tbl_barangay ORDER BY BRGY_NAME ASC");
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

            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Address</label>
                <textarea type="text" rows="4" class="form-control" name="ADDRESS" placeholder="" required></textarea>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Educational Background</label>
                <input type="text" class="form-control" name="EDUCATIONAL_BACKGROUND" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Employment History</label>
                <input type="text" class="form-control" name="EMPLOYMENT_HISTORY" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Skills and Qualifications</label>
                <input type="text" class="form-control" name="SKILLS_QUALIFICATION" placeholder="" required>
              </div>
            </div>

            <div class="col-lg-12">
              <h6 class="text-primary">APPLICATION INFORMATION</h6>
            </div>

            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Please indicate your interest in the following livelihood program(s):</label>
                <select style="width:100%" class="form-control select2" name="DESIRED_LIVELIHOOD_PROGRAM" required>
                  <option value=""></option>
                  <?php
                  $stmt = "SELECT * FROM tbl_livelihood ORDER BY LIVELIHOOD_NAME ASC";
                  $result = $conn->query($stmt);
                  while ($row = $result->fetch_assoc()) {
                  ?>
                    <option><?= $row['LIVELIHOOD_NAME']; ?></option>

                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">How much experience do you have in the chosen livelihood program(s)?</label>
                <select style="width:100%" class="form-control select2" name="EXP_LIVELIHOOD_PROGRAM_CHOSEN" required>
                  <option value=""></option>
                  <option>BEGINNER</option>
                  <option>INTERMEDIATE</option>
                  <option>ADVANCED</option>
                </select>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal"> Current Livelihood Situation</label>
                <input type="text" class="form-control" name="CURRENT_LIVELIHOOD_SITUATION" placeholder="" required>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Are you willing to commit to the required training?</label>
                <select style="width:100%" class="form-control select2" name="REQUIRED_TRAINING" required>
                  <option value=""></option>
                  <option>YES</option>
                  <option>NO</option>
                </select>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Please provide a brief reason why you are interested in participating in this livelihood program:</label>
                <textarea type="text" rows="4" class="form-control" name="REASON_INTERESTED_IN_LIVELIHOOD" placeholder="" required></textarea>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="form-group">
                <label class="font-weight-normal">List of Acceptable Valid IDs (Any of the following with one (1) photocopy)</label>
                <select style="width:100%" class="form-control select2 text-uppercase" name="VALID_ID_NUMBER" required>
                  <option value="" selected></option>
                  <!-- <option>Philippine Passport</option>
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
                        <option>National ID </option> -->
                  <?php
                  $sqlreq = "SELECT * FROM tbl_requirements ORDER BY REQ_NAME ASC";
                  $queryreq = $conn->query($sqlreq);
                  while ($reqrow = $queryreq->fetch_assoc()) {
                  ?>
                    <option><?= strtoupper($reqrow['REQ_NAME']); ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <label class="font-weight-normal">Upload valid ID</label>
                <div class="custom-file">
                  <input type="file" name="UPLOAD_ID" class="form-control custom-file-input" required>
                  <label class="custom-file-label" for="customFile">Choose file</label>
                </div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <label class="font-weight-normal">Upload ID with Selfie</label>
                <div class="custom-file">
                  <input type="file" name="UPLOAD_WITH_SELFIE" class="form-control custom-file-input" required>
                  <label class="custom-file-label" for="customFile">Choose file</label>
                </div>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="form-group">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="AGREED" required>
                  <label class="form-check-label" for="flexCheckChecked">
                    By checking this box and clicking the 'Submit' button, you confirm that you accept the Livelihood Program Privacy Policy.
                  </label>
                </div>
              </div>
            </div>

          </div><!----row-->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CLOSE</button>
          <button type="submit" class="btn btn-primary btn-sm" name="submit"><i class="fa fa-save"></i> SUBMIT</button>
      </form>
    </div>
  </div>
</div>
</div>
<!---FOR EDIT---->
<div class="modal fade" id="member_edit_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><span class="fa fa-edit"></span> UPDATE DETAILS </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal needs-validation" method="POST" action="member_edit.php" enctype="multipart/form-data" novalidate>
        <div class="modal-body text-uppercase">

          <div class="row">
            <input type="hidden" id="EDIT_MEMID" name="MEMID" required>
            <div class="col-lg-12">
              <h6 class="text-primary">PERSONAL INFORMATION</h6>
            </div>
            <div class="col-lg-4">
              <div class="form-group">

                <label for="" class="control-label font-weight-normal">First Name</label>
                <input type="text" class="form-control" id="EDIT_FNAME" name="FIRSTNAME" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Middle Name</label>
                <input type="text" class="form-control" id="EDIT_MNAME" name="MIDDLENAME" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Last Name</label>
                <input type="text" class="form-control" id="EDIT_LNAME" name="LASTNAME" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Gender</label>
                <select style="width:100%" class="form-control" name="GENDER" required>
                  <option id="EDIT_GENDER" selected></option>
                  <option>MALE</option>
                  <option>FEMALE</option>
                </select>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Date of Birth</label>
                <input type="date" id="EDIT_DOB" name="DATE_OF_BIRTH" class="form-control" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Age</label>
                <input type="text" class="form-control" id="EDIT_AGE" name="AGE" readonly required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Mobile #</label>
                <input type="text" class="form-control" id="EDIT_MOBILE" name="MOBILE" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-8">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Barangay</label>
                <select style="width:100%" class="form-control" id="EDIT_BARANGAY" name="BARANGAY" required>
                  <option value=""></option>
                  <?php
                  $mysqli = new mysqli('localhost', 'root', '', 'livelihood_database');
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

            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Address</label>
                <textarea type="text" rows="4" class="form-control" id="EDIT_ADDRESS" name="ADDRESS" placeholder="" required></textarea>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Educational Background</label>
                <input type="text" class="form-control" id="EDIT_EDUCATION" name="EDUCATIONAL_BACKGROUND" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Employment History</label>
                <input type="text" class="form-control" id="EDIT_EMPLOYMENT" name="EMPLOYMENT_HISTORY" placeholder="" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Skills and Qualifications</label>
                <input type="text" class="form-control" id="EDIT_SKILLS" name="SKILLS_QUALIFICATION" placeholder="" required>
              </div>
            </div>

            <div class="col-lg-12">
              <h6 class="text-primary">APPLICATION INFORMATION</h6>
            </div>

            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Please indicate your interest in the following livelihood program(s):</label>
                <select style="width:100%" class="form-control" name="DESIRED_LIVELIHOOD_PROGRAM" required>
                  <option id="EDIT_DESIRE"></option>
                  <?php
                  $stmt = $mysqli->prepare("SELECT * FROM tbl_livelihood ORDER BY LIVELIHOOD_NAME ASC");
                  if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {

                        print '<option value=' . ucwords($row['LIVELIHOOD_NAME']) . '>' . strtoupper(ucwords($row['LIVELIHOOD_NAME'])) . '</option>';
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
                <label for="" class="control-label font-weight-normal">How much experience do you have in the chosen livelihood program(s)?</label>
                <select style="width:100%" class="form-control" name="EXP_LIVELIHOOD_PROGRAM_CHOSEN" required>
                  <option id="EDIT_PROGRAMCHOSEN" selected></option>
                  <option>BEGINNER</option>
                  <option>INTERMEDIATE</option>
                  <option>ADVANCED</option>
                </select>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal"> Current Livelihood Situation</label>
                <input type="text" class="form-control" id="EDIT_CURRENTLIVELI" name="CURRENT_LIVELIHOOD_SITUATION" placeholder="" required>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Are you willing to commit to the required training?</label>
                <select style="width:100%" class="form-control" name="REQUIRED_TRAINING" required>
                  <option id="EDIT_REQTRAINING"></option>
                  <option>YES</option>
                  <option>NO</option>
                </select>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Please provide a brief reason why you are interested in participating in this livelihood program:</label>
                <textarea type="text" rows="4" class="form-control" id="EDIT_REASONINTEREST" name="REASON_INTERESTED_IN_LIVELIHOOD" placeholder="" required></textarea>
              </div>
            </div>


          </div><!----row-->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CLOSE</button>
          <button type="submit" class="btn btn-primary btn-sm" name="submit"><i class="fa fa-save"></i> SUBMIT</button>
      </form>
    </div>
  </div>
</div>
</div>
<!---FOR DELETE---->
<div class="modal fade" id="member_delete_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class="fa fa-trash"></span> PLEASE CONFIRM</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <form action="member_delete.php" method="POST">
          <input type="hidden" id="delete_memid" name="MEMID">
          <p><strong>Are you sure you want to delete this member?</strong></p>
          <p><span id="delete_membername" class="text-danger font-weight-bold"></span></p>
      </div>
      <div class="modal-footer">
        <button type="submit" name="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> DELETE</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CANCEL</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!---FOR ARCHIVE---->
<div class="modal fade" id="member_archive_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class="fa fa-archive"></span> PLEASE CONFIRM</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <form action="member_archive.php" method="POST">
          <input type="hidden" id="archive_memid" name="MEMID">
          <p><strong>Are you sure you want to archive this member?</strong></p>
          <p><span id="archive_membername" class="text-danger font-weight-bold"></span></p>
          <div class="form-group text-left">
            <label for="remarks">Remarks (optional)</label>
            <textarea name="STATUS_REMARKS" class="form-control" rows="3" placeholder="Reason or notes for archiving..."></textarea>
          </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="submit" class="btn btn-warning btn-sm">
          <i class="fa fa-archive"></i> ARCHIVE
        </button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
          <i class="fa fa-times"></i> CANCEL
        </button>
      </div>
      </form>
    </div>
  </div>
</div>

<!---FOR UNARCHIVE---->
<div class="modal fade" id="member_unarchive_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class="fa fa-undo"></span> UNARCHIVE MEMBER</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <form action="member_unarchive.php" method="POST">
          <input type="hidden" id="unarchive_memid" name="MEMID">
          <strong>Are you sure you want to unarchive this member?</strong><br>
          <span id="unarchive_membername"></span>
      </div>
      <div class="modal-footer">
        <button type="submit" name="submit" class="btn btn-success btn-sm">
          <i class="fa fa-check"></i> Confirm
        </button>
      </div>
      </form>
    </div>
  </div>
</div>

<!---FOR ACTIONS---->
<div class="modal fade" id="member_actions_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class="fa fa-alert-exclamation" id="actions_membername"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="needs-validation" action="member_actions.php" method="POST" novalidate>
        <div class="modal-body">
          <input type="hidden" id="actions_memid" name="MEMID">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">Please select Status</label>
                <select style="width:100%" class="form-control" name="STATUS" required>
                  <option id="actions_membstatus"></option>
                  <option>PENDING</option>
                  <option>APPROVED</option>
                  <option>DEACTIVE</option>
                  <option>REJECTED</option>
                  <option>ARCHIVED</option>
                </select>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="" class="control-label font-weight-normal">REMARKS</label>
                <textarea rows="4" name="STATUS_REMARKS" id="actions_memremarks" class="form-control" required></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> SUBMIT</button>
        </div>
      </form>
    </div>
  </div>
</div>