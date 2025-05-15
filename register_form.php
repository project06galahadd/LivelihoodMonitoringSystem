<?php include "header.php";
include "conn.php";
include "conn_member.php";
include "conn_admin.php";
include "register_check_";
include "register_process";
include "register_script"; ?>

<body>
  <div class="wrapper">
    <?php include "navbar.php"; ?>

    <div class="content container mt-5">
      <div class="container">
        <div class="col-md-12">
          <h4 class="text-white">SIGN UP</h4>
          <div class="sticky-tops">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Please create your account credentials first</h4>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" onclick="window.location.href='index.php';" title="Close">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <form class="needs-validation" method="POST" action="register_process.php" enctype="multipart/form-data" novalidate id="registrationForm">
                <div class="card-body text-uppercase">
                  <div id="step-credentials">
                    <div class="row">
                      <div class="col-lg-12">
                        <h6 class="text-primary">ACCOUNT CREDENTIALS</h6>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Username</label>
                          <input type="text" class="form-control" name="USERNAME" required minlength="6">
                          <small class="form-text text-muted">Choose a unique username (minimum 6 characters)</small>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Email Address</label>
                          <input type="email" class="form-control" name="EMAIL" required>
                          <small class="form-text text-muted">This will be used for account recovery</small>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Password</label>
                          <input type="password" class="form-control" name="PASSWORD" required minlength="8">
                          <small class="form-text text-muted">Minimum 8 characters with numbers and letters</small>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Confirm Password</label>
                          <input type="password" class="form-control" name="CONFIRM_PASSWORD" required minlength="8">
                          <small class="form-text text-muted">Re-enter your password</small>
                        </div>
                      </div>
                      <div class="col-lg-12 mt-3">
                        <button type="button" class="btn btn-primary btn-block" id="nextToPersonal">Next</button>
                      </div>
                    </div>
                  </div>
                  <div id="step-personal" style="display:none;">
                    <div class="row">
                      <div class="col-lg-12 mt-4">
                        <h6 class="text-primary">PERSONAL INFORMATION</h6>
                      </div>
                      <input type="hidden" class="form-control" name="AUTO_NUMBER" value="<?= $number; ?>" required>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>First Name</label>
                          <input type="text" class="form-control" name="FIRSTNAME" required>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>Middle Name</label>
                          <input type="text" class="form-control" name="MIDDLENAME" required>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>Last Name</label>
                          <input type="text" class="form-control" name="LASTNAME" required>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>Gender</label>
                          <select class="form-control" name="GENDER" required>
                            <option value=""></option>
                            <option>MALE</option>
                            <option>FEMALE</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>Date of Birth</label>
                          <input type="date" class="form-control" name="DATE_OF_BIRTH" required>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>Age</label>
                          <input type="text" class="form-control" name="AGE" readonly required>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>Mobile #</label>
                          <input type="text" class="form-control" name="MOBILE" required>
                        </div>
                      </div>
                      <div class="col-lg-8">
                        <div class="form-group">
                          <label>Barangay</label>
                          <select class="form-control select2" name="BARANGAY" required>
                            <option value=""></option>
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM tbl_barangay ORDER BY BRGY_NAME ASC");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                              echo '<option value="' . htmlspecialchars($row['BRGY_NAME']) . '">' . htmlspecialchars($row['BRGY_NAME']) . '</option>';
                            }
                            $stmt->close();
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label>Address</label>
                          <textarea rows="4" class="form-control" name="ADDRESS" required></textarea>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>Educational Background</label>
                          <input type="text" class="form-control" name="EDUCATIONAL_BACKGROUND" required>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>Employment History</label>
                          <input type="text" class="form-control" name="EMPLOYMENT_HISTORY" required>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>Skills and Qualifications</label>
                          <input type="text" class="form-control" name="SKILLS_QUALIFICATION" required>
                        </div>
                      </div>
                      <div class="col-lg-12">
                        <h6 class="text-primary">APPLICATION INFORMATION</h6>
                      </div>
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label>Desired Livelihood Program</label>
                          <select class="form-control select2" name="DESIRED_LIVELIHOOD_PROGRAM" required>
                            <option value=""></option>
                            <?php
                            $stmt = $conn->query("SELECT * FROM tbl_livelihood ORDER BY LIVELIHOOD_NAME ASC");
                            while ($row = $stmt->fetch_assoc()) {
                              echo '<option>' . htmlspecialchars($row['LIVELIHOOD_NAME']) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label>Experience Level</label>
                          <select class="form-control select2" name="EXP_LIVELIHOOD_PROGRAM_CHOSEN" required>
                            <option value=""></option>
                            <option>BEGINNER</option>
                            <option>INTERMEDIATE</option>
                            <option>ADVANCED</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label>Current Livelihood Situation</label>
                          <input type="text" class="form-control" name="CURRENT_LIVELIHOOD_SITUATION" required>
                        </div>
                      </div>
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label>Willing to Commit to Training?</label>
                          <select class="form-control select2" name="REQUIRED_TRAINING" required>
                            <option value=""></option>
                            <option>YES</option>
                            <option>NO</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label>Why are you interested?</label>
                          <textarea rows="4" class="form-control" name="REASON_INTERESTED_IN_LIVELIHOOD" required></textarea>
                        </div>
                      </div>
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label>Valid ID Type</label>
                          <select class="form-control select2 text-uppercase" name="VALID_ID_NUMBER" required>
                            <option value=""></option>
                            <?php
                            $query = $conn->query("SELECT * FROM tbl_requirements ORDER BY REQ_NAME ASC");
                            while ($reqrow = $query->fetch_assoc()) {
                              echo '<option>' . strtoupper(htmlspecialchars($reqrow['REQ_NAME'])) . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Upload Valid ID</label>
                          <div class="custom-file">
                            <input type="file" name="UPLOAD_ID" class="form-control custom-file-input" accept="image/*" required>
                            <label class="custom-file-label">Choose file</label>
                          </div>
                          <small class="form-text text-muted">Maximum file size: 2MB. Allowed types: JPG, JPEG, PNG, GIF</small>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Upload Selfie with ID</label>
                          <div class="custom-file">
                            <input type="file" name="UPLOAD_WITH_SELFIE" class="form-control custom-file-input" accept="image/*" required>
                            <label class="custom-file-label">Choose file</label>
                          </div>
                          <small class="form-text text-muted">Please take a selfie while holding your ID. Maximum file size: 2MB</small>
                        </div>
                      </div>
                      <div class="col-lg-12 mt-3">
                        <div id="response-message" class="mb-3"></div>
                        <button type="button" class="btn btn-secondary btn-block mb-2" onclick="window.print();">Print Form</button>
                        <button type="submit" class="btn btn-primary btn-block" id="submitBtn">Submit Application</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>

              <script>
              // Step navigation logic
              const credentialsStep = document.getElementById('step-credentials');
              const personalStep = document.getElementById('step-personal');
              const nextBtn = document.getElementById('nextToPersonal');

              nextBtn.addEventListener('click', function() {
                // Validate credentials fields
                const form = document.getElementById('registrationForm');
                const username = form.USERNAME;
                const email = form.EMAIL;
                const password = form.PASSWORD;
                const confirmPassword = form.CONFIRM_PASSWORD;
                let valid = true;

                // Username validation
                if (username.value.length < 6) {
                  username.setCustomValidity('Username must be at least 6 characters long');
                  valid = false;
                } else {
                  username.setCustomValidity('');
                }

                // Email validation
                if (!email.value.match(/^\S+@\S+\.\S+$/)) {
                  email.setCustomValidity('Enter a valid email address');
                  valid = false;
                } else {
                  email.setCustomValidity('');
                }

                // Password validation
                if (password.value.length < 8) {
                  password.setCustomValidity('Password must be at least 8 characters long');
                  valid = false;
                } else if (!/[0-9]/.test(password.value) || !/[a-zA-Z]/.test(password.value)) {
                  password.setCustomValidity('Password must contain both letters and numbers');
                  valid = false;
                } else {
                  password.setCustomValidity('');
                }

                // Confirm password
                if (confirmPassword.value !== password.value) {
                  confirmPassword.setCustomValidity('Passwords do not match');
                  valid = false;
                } else {
                  confirmPassword.setCustomValidity('');
                }

                if (valid) {
                  credentialsStep.style.display = 'none';
                  personalStep.style.display = 'block';
                  window.scrollTo(0, 0);
                } else {
                  form.classList.add('was-validated');
                }
              });

              // Update file input labels
              document.querySelectorAll('.custom-file-input').forEach(input => {
                input.addEventListener('change', function(e) {
                  const fileName = e.target.files[0]?.name || 'Choose file';
                  const label = e.target.nextElementSibling;
                  label.textContent = fileName;
                  
                  // Add Bootstrap class for showing the file name
                  if (fileName !== 'Choose file') {
                    label.classList.add('selected');
                  } else {
                    label.classList.remove('selected');
                  }
                });
              });

              // Form submission handling
              document.getElementById('registrationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate form
                if (!this.checkValidity()) {
                  e.stopPropagation();
                  this.classList.add('was-validated');
                  return;
                }

                const formData = new FormData(this);
                const submitBtn = document.getElementById('submitBtn');
                const responseMessage = document.getElementById('response-message');
                
                // Validate file sizes
                const idFile = this.querySelector('input[name="UPLOAD_ID"]').files[0];
                const selfieFile = this.querySelector('input[name="UPLOAD_WITH_SELFIE"]').files[0];
                
                if (idFile && idFile.size > 2 * 1024 * 1024) {
                  responseMessage.innerHTML = '<div class="alert alert-danger">ID file size exceeds 2MB limit</div>';
                  return;
                }
                
                if (selfieFile && selfieFile.size > 2 * 1024 * 1024) {
                  responseMessage.innerHTML = '<div class="alert alert-danger">Selfie file size exceeds 2MB limit</div>';
                  return;
                }
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
                
                fetch('register_process.php', {
                  method: 'POST',
                  body: formData
                })
                .then(response => {
                  if (!response.ok) {
                    throw new Error('Network response was not ok');
                  }
                  return response.json();
                })
                .then(data => {
                  if (data.status === 'success') {
                    responseMessage.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    if (data.redirect) {
                      window.location.href = data.redirect;
                    }
                  } else {
                    responseMessage.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit Application';
                  }
                })
                .catch(error => {
                  console.error('Error:', error);
                  responseMessage.innerHTML = '<div class="alert alert-danger">An error occurred while submitting the form. Please try again.</div>';
                  submitBtn.disabled = false;
                  submitBtn.innerHTML = 'Submit Application';
                });
              });
              </script>

              <style>
                /* Print Styles */
                @media print {
                  body * {
                    visibility: hidden;
                  }
                  .print-content, .print-content * {
                    visibility: visible;
                  }
                  .print-content {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                  }
                  .print-header {
                    display: block !important;
                    margin-bottom: 2rem;
                  }
                  .print-logo {
                    max-width: 150px;
                    height: auto;
                  }
                  .print-hr {
                    border-top: 2px solid #000;
                    margin: 1rem 0;
                  }
                  .form-group {
                    margin-bottom: 1rem;
                  }
                  .form-control {
                    border: 1px solid #000;
                    background: transparent;
                    padding: 0.5rem;
                  }
                  .form-control:focus {
                    box-shadow: none;
                  }
                  .card {
                    border: none;
                    box-shadow: none;
                  }
                  .card-header, .card-tools {
                    display: none;
                  }
                  .btn {
                    display: none;
                  }
                  .custom-file {
                    display: none;
                  }
                  .text-muted {
                    color: #000 !important;
                  }
                  .text-primary {
                    color: #000 !important;
                    font-weight: bold;
                  }
                  .mt-4 {
                    margin-top: 2rem !important;
                  }
                  .mb-3 {
                    margin-bottom: 1rem !important;
                  }
                  .col-lg-12 {
                    page-break-inside: avoid;
                  }
                }

                /* Screen Styles */
                .custom-file-label.selected {
                  color: #495057;
                  background-color: #fff;
                  border-color: #80bdff;
                  box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
                }
              </style>

              <script>
                function printForm() {
                  const printContent = document.querySelector('.card-body').cloneNode(true);
                  printContent.classList.add('print-content');
                  
                  const printHeader = document.createElement('div');
                  printHeader.className = 'print-header';
                  printHeader.innerHTML = `
                    <div class="document-number" style="text-align: right; font-size: 10px; margin-bottom: 5px;">Document No: ${new Date().getTime()}</div>
                    <div class="document-date" style="text-align: right; font-size: 10px; margin-bottom: 10px;">Date: ${new Date().toLocaleDateString()}</div>
                    <div style="text-align: center;">
                      <img src="Logo.png" alt="MSWD Logo" style="width: 90px; height: auto; display: block; margin: 0 auto 10px auto;">
                      <h2 style="font-size: 16px; margin: 10px 0;">Municipal Social Welfare and Development</h2>
                      <h3 style="font-size: 14px; margin-bottom: 10px;">LIVELIHOOD PROGRAM REGISTRATION FORM</h3>
                      <hr style="border-top: 1px solid #000; margin: 10px 0;">
                    </div>
                  `;
                  printContent.insertBefore(printHeader, printContent.firstChild);
                  
                  // Add signature section
                  const signatureSection = document.createElement('div');
                  signatureSection.className = 'text-center';
                  signatureSection.innerHTML = `
                    <div style="border-top: 1px solid #000; width: 200px; margin: 20px auto;"></div>
                    <div style="font-size: 10px;">Applicant's Signature</div>
                    <div style="border-top: 1px solid #000; width: 200px; margin: 20px auto;"></div>
                    <div style="font-size: 10px;">MSWD Officer's Signature</div>
                  `;
                  printContent.appendChild(signatureSection);
                  
                  // Create preview window
                  const previewWindow = window.open('', '_blank', 'width=800,height=600');
                  previewWindow.document.write(`
                    <html>
                      <head>
                        <title>MSWD Registration Form</title>
                        <style>
                          body {
                            font-family: Arial, sans-serif;
                            font-size: 12px;
                            line-height: 1.5;
                            padding: 20px;
                            background: #f5f5f5;
                          }
                          .print-content {
                            background: white;
                            padding: 20px;
                            box-shadow: 0 0 10px rgba(0,0,0,0.1);
                            max-width: 800px;
                            margin: 0 auto;
                          }
                          .form-group {
                            margin-bottom: 10px;
                          }
                          .form-control {
                            border: 1px solid #000;
                            padding: 5px;
                            width: 100%;
                            background: transparent;
                          }
                          .text-primary {
                            font-weight: bold;
                            color: #000;
                          }
                          .section-title {
                            font-weight: bold;
                            text-decoration: underline;
                            margin: 15px 0;
                          }
                          .document-number, .document-date {
                            text-align: right;
                            font-size: 10px;
                            margin-bottom: 5px;
                          }
                          .print-header img {
                            display: block;
                            margin: 0 auto 10px auto;
                          }
                          @media print {
                            body {
                              background: white;
                              padding: 0;
                            }
                            .print-content {
                              box-shadow: none;
                              padding: 0;
                            }
                            button { display: none; }
                          }
                        </style>
                      </head>
                      <body>
                        <div class="print-content">
                          ${printContent.innerHTML}
                        </div>
                        <div style="text-align: center; margin-top: 20px;">
                          <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">Print Form</button>
                        </div>
                      </body>
                    </html>
                  `);
                }

                document.querySelector('button[onclick="window.print();"]').onclick = function(e) {
                  e.preventDefault();
                  printForm();
                };
              </script>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php include "footer.php"; ?>
  </div>
</body>