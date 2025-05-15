<!-- Initialize custom file input and Select2 -->
<script>
  $(function() {
    bsCustomFileInput.init();

    $('.select2').select2({
      theme: 'bootstrap4',
      placeholder: 'Select an option',
      allowClear: true
    });

    // Add custom validation for password
    $.validator.addMethod("passwordStrength", function(value, element) {
      return this.optional(element) || /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/.test(value);
    }, "Password must be at least 8 characters and contain both letters and numbers");

    // Add custom validation for file size
    $.validator.addMethod("fileSize", function(value, element, param) {
      return this.optional(element) || (element.files[0].size <= param * 1024 * 1024);
    }, "File size must be less than {0} MB");

    // Initialize form validation
    $("#registrationForm").validate({
      rules: {
        USERNAME: {
          required: true,
          minlength: 6
        },
        EMAIL: {
          required: true,
          email: true
        },
        PASSWORD: {
          required: true,
          minlength: 8,
          passwordStrength: true
        },
        CONFIRM_PASSWORD: {
          required: true,
          equalTo: "#PASSWORD"
        },
        FIRSTNAME: "required",
        LASTNAME: "required",
        GENDER: "required",
        DATE_OF_BIRTH: "required",
        MOBILE: "required",
        BARANGAY: "required",
        ADDRESS: "required",
        EDUCATIONAL_BACKGROUND: "required",
        EMPLOYMENT_HISTORY: "required",
        SKILLS_QUALIFICATION: "required",
        DESIRED_LIVELIHOOD_PROGRAM: "required",
        EXP_LIVELIHOOD_PROGRAM_CHOSEN: "required",
        CURRENT_LIVELIHOOD_SITUATION: "required",
        REQUIRED_TRAINING: "required",
        REASON_INTERESTED_IN_LIVELIHOOD: "required",
        VALID_ID_NUMBER: "required",
        UPLOAD_ID: {
          required: true,
          accept: "image/*",
          fileSize: 2
        },
        UPLOAD_WITH_SELFIE: {
          required: true,
          accept: "image/*",
          fileSize: 2
        }
      },
      messages: {
        USERNAME: {
          required: "Please enter a username",
          minlength: "Username must be at least 6 characters long"
        },
        EMAIL: {
          required: "Please enter your email address",
          email: "Please enter a valid email address"
        },
        PASSWORD: {
          required: "Please enter a password",
          minlength: "Password must be at least 8 characters long"
        },
        CONFIRM_PASSWORD: {
          required: "Please confirm your password",
          equalTo: "Passwords do not match"
        },
        UPLOAD_ID: {
          required: "Please upload your valid ID",
          accept: "Please upload a valid image file",
          fileSize: "File size must be less than 2 MB"
        },
        UPLOAD_WITH_SELFIE: {
          required: "Please upload your selfie with ID",
          accept: "Please upload a valid image file",
          fileSize: "File size must be less than 2 MB"
        }
      },
      errorElement: 'span',
      errorPlacement: function(error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function(element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function(element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      },
      submitHandler: function(form) {
        const formData = new FormData(form);
        const submitBtn = $("#submitBtn");
        const responseMessage = $("#response-message");
        
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
        
        $.ajax({
          url: "register_process.php",
          type: "POST",
          data: formData,
          contentType: false,
          cache: false,
          processData: false,
          dataType: 'json',
          success: function(response) {
            if (response.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Registration Successful',
                text: response.message,
                showConfirmButton: false,
                timer: 2000
              }).then(function() {
                window.location.href = response.redirect;
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Registration Failed',
                text: response.message
              });
              submitBtn.prop('disabled', false).html('Submit Application');
            }
          },
          error: function(xhr, status, error) {
            console.error("Error:", error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An error occurred while submitting your application. Please try again later.'
            });
            submitBtn.prop('disabled', false).html('Submit Application');
          }
        });
      }
    });

    // Auto-calculate age from date of birth
    $('input[name="DATE_OF_BIRTH"]').on('change', function() {
      const dob = new Date(this.value);
      const today = new Date();
      let age = today.getFullYear() - dob.getFullYear();
      const monthDiff = today.getMonth() - dob.getMonth();
      
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
      }
      
      $('input[name="AGE"]').val(age);
    });

    // Update file input labels
    $('.custom-file-input').on('change', function() {
      const fileName = $(this).val().split('\\').pop();
      $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Choose file');
    });
  });
</script>

<!-- Show Terms and Conditions with SweetAlert -->
<script>
  $("#link").click(function() {
    Swal.fire({
      icon: "info",
      title: "TERMS AND CONDITIONS",
      html: "By continuing this application, I agree that my personal info can be used for the MSWD Appointment Schedule. I understand that this means I'm giving up some privacy rights regarding how my info is used, based on the rules in the MSWD Appointment and Scheduling System Website and other relevant regulations.",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Agree"
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "register_form.php?register_form";
      }
    });
  });
</script>

<!-- Bootstrap 4 Form Validation & AJAX Submission for Viewing Form -->
<script>
  $(".view_form").on("submit", function(event) {
    event.preventDefault(); // Always prevent default
    var form = this;

    if (!form.checkValidity()) {
      event.stopPropagation();
    } else {
      let formData = new FormData(form);

      $.ajax({
        url: "view_form_process.php",
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(response) {
          $("#view_form").html(response);
        },
        error: function(xhr, status, error) {
          console.error("Error:", error);
          console.error("Response:", xhr.responseText);
        }
      });
    }

    form.classList.add("was-validated");
  });
</script>