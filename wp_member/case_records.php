<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get all case records for the current user
$stmt = $conn->prepare("SELECT * FROM tbl_household_case_records WHERE submitted_by = ? ORDER BY date_created DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Records - Livelihood Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .table-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 2rem auto;
        }
        .btn-add {
            background-color: #28a745;
            color: white;
        }
        .btn-add:hover {
            background-color: #218838;
            color: white;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        .status-approved {
            background-color: #28a745;
            color: #fff;
        }
        .status-rejected {
            background-color: #dc3545;
            color: #fff;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        .modal-lg {
            max-width: 800px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .was-validated .form-control:invalid,
        .was-validated .form-select:invalid {
            border-color: #dc3545;
        }
        .was-validated .form-control:valid,
        .was-validated .form-select:valid {
            border-color: #198754;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-file-alt me-2"></i>Case Records</h2>
                <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#caseFormModal">
                    <i class="fas fa-plus me-2"></i>Add New Case
                </button>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Case ID</th>
                                <th>Household Head</th>
                                <th>Case Type</th>
                                <th>Family Size</th>
                                <th>Monthly Income</th>
                                <th>Status</th>
                                <th>Date Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['record_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['household_head']); ?></td>
                                    <td><?php echo htmlspecialchars($row['case_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['family_size']); ?></td>
                                    <td>₱<?php echo number_format($row['monthly_income'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($row['date_created'])); ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'PENDING'): ?>
                                            <button type="button" class="btn btn-sm btn-primary edit-case" data-id="<?php echo $row['record_id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-info view-details" data-id="<?php echo $row['record_id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h4>No Case Records Found</h4>
                    <p class="text-muted">Start by adding a new case record.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Case Form Modal -->
    <div class="modal fade" id="caseFormModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="caseFormTitle">New Case Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="caseForm" class="needs-validation" novalidate>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="record_id" value="">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="household_head" class="form-label required-field">Household Head</label>
                                <input type="text" class="form-control" id="household_head" name="household_head" required>
                                <div class="invalid-feedback">Please enter the household head's name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="case_type" class="form-label required-field">Case Type</label>
                                <select class="form-select" id="case_type" name="case_type" required>
                                    <option value="">Select Case Type</option>
                                    <option value="Financial Assistance">Financial Assistance</option>
                                    <option value="Livelihood Support">Livelihood Support</option>
                                    <option value="Emergency Aid">Emergency Aid</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback">Please select a case type.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label required-field">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                            <div class="invalid-feedback">Please enter the address.</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                       pattern="[0-9+\-\s()]{10,15}">
                                <div class="form-text">Format: 10-15 digits, may include +, -, spaces, and parentheses</div>
                            </div>
                            <div class="col-md-6">
                                <label for="family_size" class="form-label required-field">Family Size</label>
                                <input type="number" class="form-control" id="family_size" name="family_size" 
                                       min="1" required>
                                <div class="invalid-feedback">Please enter a valid family size.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="monthly_income" class="form-label required-field">Monthly Income (PHP)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="monthly_income" name="monthly_income" 
                                       min="0" step="0.01" required>
                                <div class="invalid-feedback">Please enter a valid monthly income.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="case_details" class="form-label required-field">Case Details</label>
                            <textarea class="form-control" id="case_details" name="case_details" rows="4" required></textarea>
                            <div class="invalid-feedback">Please enter case details.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitCase">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Case Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="caseDetails"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const caseFormModal = new bootstrap.Modal(document.getElementById('caseFormModal'));
            const viewDetailsModal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
            const form = document.getElementById('caseForm');
            
            // Reset form when modal is closed
            $('#caseFormModal').on('hidden.bs.modal', function() {
                form.reset();
                form.classList.remove('was-validated');
                $('#caseFormTitle').text('New Case Record');
                $('#caseForm input[name="action"]').val('add');
                $('#caseForm input[name="record_id"]').val('');
            });

            // Submit case form
            $('#submitCase').click(function() {
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }

                const formData = new FormData(form);
                
                $.ajax({
                    url: 'process_case.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            if (result.success) {
                                alert(result.message);
                                location.reload();
                            } else {
                                alert(result.message || 'An error occurred while processing your request.');
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('An error occurred while processing your request.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        console.log('Response:', xhr.responseText);
                        alert('An error occurred while processing your request.');
                    }
                });
            });

            // Edit case
            $('.edit-case').click(function() {
                const recordId = $(this).data('id');
                $.ajax({
                    url: 'get_case_details.php',
                    method: 'GET',
                    data: { id: recordId },
                    success: function(response) {
                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            if (result.success) {
                                const record = result.record;
                                $('#caseFormTitle').text('Edit Case Record');
                                $('#caseForm input[name="action"]').val('edit');
                                $('#caseForm input[name="record_id"]').val(recordId);
                                $('#household_head').val(record.household_head);
                                $('#case_type').val(record.case_type);
                                $('#address').val(record.address);
                                $('#contact_number').val(record.contact_number);
                                $('#family_size').val(record.family_size);
                                $('#monthly_income').val(record.monthly_income);
                                $('#case_details').val(record.case_details);
                                caseFormModal.show();
                            } else {
                                alert(result.message || 'Error loading case details');
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('Error loading case details');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error loading case details');
                    }
                });
            });

            // View Details
            $('.view-details').click(function() {
                const recordId = $(this).data('id');
                $.ajax({
                    url: 'get_case_details.php',
                    method: 'GET',
                    data: { id: recordId },
                    success: function(response) {
                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            if (result.success) {
                                $('#caseDetails').html(result.html);
                                viewDetailsModal.show();
                            } else {
                                alert(result.message || 'Error loading case details');
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('Error loading case details');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error loading case details');
                    }
                });
            });
        });
    </script>
</body>
</html> 