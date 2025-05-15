<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Database connection
require_once '../config/database.php';

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get record ID if editing
$record_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$record = null;

if ($record_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_household_case_records WHERE record_id = ? AND submitted_by = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ii", $record_id, $_SESSION['user_id']);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $record = $result->fetch_assoc();
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error in case_form.php: " . $e->getMessage());
        die("An error occurred while retrieving the record. Please try again later.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $record_id ? 'Edit Case' : 'New Case'; ?> - Livelihood Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 500;
            color: #333;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        .btn-submit {
            background-color: #0d6efd;
            color: white;
            padding: 0.5rem 2rem;
        }
        .btn-submit:hover {
            background-color: #0b5ed7;
            color: white;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">
                <i class="fas fa-file-alt me-2"></i>
                <?php echo $record_id ? 'Edit Case Record' : 'New Case Record'; ?>
            </h2>
            
            <form id="caseForm" method="POST" action="process_case.php">
                <input type="hidden" name="action" value="<?php echo $record_id ? 'edit' : 'add'; ?>">
                <?php if ($record_id): ?>
                    <input type="hidden" name="record_id" value="<?php echo $record_id; ?>">
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="household_head" class="form-label required-field">Household Head</label>
                        <input type="text" class="form-control" id="household_head" name="household_head" 
                               value="<?php echo $record ? htmlspecialchars($record['household_head']) : ''; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="case_type" class="form-label required-field">Case Type</label>
                        <select class="form-select" id="case_type" name="case_type" required>
                            <option value="">Select Case Type</option>
                            <option value="Financial Assistance" <?php echo ($record && $record['case_type'] == 'Financial Assistance') ? 'selected' : ''; ?>>Financial Assistance</option>
                            <option value="Livelihood Support" <?php echo ($record && $record['case_type'] == 'Livelihood Support') ? 'selected' : ''; ?>>Livelihood Support</option>
                            <option value="Emergency Aid" <?php echo ($record && $record['case_type'] == 'Emergency Aid') ? 'selected' : ''; ?>>Emergency Aid</option>
                            <option value="Other" <?php echo ($record && $record['case_type'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label required-field">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2" required><?php echo $record ? htmlspecialchars($record['address']) : ''; ?></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                               value="<?php echo $record ? htmlspecialchars($record['contact_number']) : ''; ?>"
                               pattern="[0-9+\-\s()]{10,15}">
                        <div class="form-text">Format: 10-15 digits, may include +, -, spaces, and parentheses</div>
                    </div>
                    <div class="col-md-6">
                        <label for="family_size" class="form-label required-field">Family Size</label>
                        <input type="number" class="form-control" id="family_size" name="family_size" 
                               value="<?php echo $record ? htmlspecialchars($record['family_size']) : ''; ?>" 
                               min="1" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="monthly_income" class="form-label required-field">Monthly Income (PHP)</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control" id="monthly_income" name="monthly_income" 
                               value="<?php echo $record ? htmlspecialchars($record['monthly_income']) : ''; ?>" 
                               min="0" step="0.01" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="case_details" class="form-label required-field">Case Details</label>
                    <textarea class="form-control" id="case_details" name="case_details" rows="4" required><?php echo $record ? htmlspecialchars($record['case_details']) : ''; ?></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save me-2"></i>
                        <?php echo $record_id ? 'Update Case' : 'Submit Case'; ?>
                    </button>
                    <a href="case_records.php" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#caseForm').on('submit', function(e) {
                e.preventDefault();
                
                // Basic form validation
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                // Submit form via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = 'case_records.php';
                        } else {
                            alert(response.message || 'An error occurred while processing your request.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('An error occurred while processing your request. Please try again later.');
                    }
                });
            });
        });
    </script>
</body>
</html> 