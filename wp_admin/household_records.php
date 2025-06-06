<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/session.php';
require_once 'includes/conn.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/sidebar.php';

// Get all household case records
try {
    $sql = "SELECT h.*, 
            CONCAT(u.FIRSTNAME, ' ', u.LASTNAME) as submitted_by_name,
            CONCAT(u2.FIRSTNAME, ' ', u2.LASTNAME) as approved_by_name
            FROM tbl_household_case_records h 
            LEFT JOIN tbl_users u ON h.submitted_by = u.ID 
            LEFT JOIN tbl_users u2 ON h.approved_by = u2.ID 
            ORDER BY h.date_created DESC";

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Error executing query: " . $conn->error);
    }
} catch (Exception $e) {
    error_log("Error in household records: " . $e->getMessage());
    $result = null;
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Household Case Records</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Household Case Records</h3>
                        </div>
                        <div class="card-body">
                            <table id="household-table" class="table table-bordered table-striped table-sm text-sm">
                                <thead>
                                    <tr>
                                        <th>Date Submitted</th>
                                        <th>Submitted By</th>
                                        <th>Beneficiary Name</th>
                                        <th>Address</th>
                                        <th>Problem Presented</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $status_class = '';
                                            switch ($row['status']) {
                                                case 'PENDING':
                                                    $status_class = 'pending';
                                                    break;
                                                case 'APPROVED':
                                                    $status_class = 'approved';
                                                    break;
                                                case 'REJECTED':
                                                    $status_class = 'rejected';
                                                    break;
                                            }
                                    ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($row['date_created'])); ?></td>
                                                <td><?php echo htmlspecialchars($row['submitted_by_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['beneficiary_lastname'] . ', ' . $row['beneficiary_firstname'] . ' ' . $row['beneficiary_middlename']); ?></td>
                                                <td><?php echo htmlspecialchars($row['complete_address']); ?></td>
                                                <td><?php echo htmlspecialchars($row['problem_presented']); ?></td>
                                                <td><span class="status-badge status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                                                <td><?php echo htmlspecialchars($row['remarks'] ? $row['remarks'] : 'No remarks'); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick="viewCase(<?php echo $row['id']; ?>)">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <?php if ($row['status'] === 'PENDING'): ?>
                                                        <button class="btn btn-sm btn-success" onclick="approveCase(<?php echo $row['id']; ?>)">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="rejectCase(<?php echo $row['id']; ?>)">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    <?php elseif ($row['status'] === 'APPROVED'): ?>
                                                        <button class="btn btn-sm btn-primary" onclick="editCase(<?php echo $row['id']; ?>)">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-secondary" onclick="viewHistory(<?php echo $row['id']; ?>)">
                                                            <i class="fas fa-history"></i> History
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center'>No household case records found</td></tr>";
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

<!-- View Case Modal -->
<div class="modal fade" id="viewCaseModal" tabindex="-1" role="dialog" aria-labelledby="viewCaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="viewCaseModalLabel">View Household Case Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Date Submitted:</strong> <span id="view_date_submitted"></span></p>
                                <p><strong>Submitted By:</strong> <span id="view_submitted_by"></span></p>
                                <p><strong>Beneficiary Name:</strong> <span id="view_beneficiary_name"></span></p>
                                <p><strong>Status:</strong> <span id="view_status"></span></p>
                                <p><strong>Contact Number:</strong> <span id="view_contact_number"></span></p>
                                <p><strong>Monthly Income:</strong> <span id="view_monthly_income"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Approval Information</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Date Approved:</strong> <span id="view_date_approved"></span></p>
                                <p><strong>Approved By:</strong> <span id="view_approved_by"></span></p>
                                <p><strong>Last Updated:</strong> <span id="view_last_updated"></span></p>
                                <p><strong>Case Number:</strong> <span id="view_case_number"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Address Information</h6>
                            </div>
                            <div class="card-body">
                                <p id="view_address"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Case Details</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Problem Presented:</strong></p>
                                <p id="view_problem"></p>
                                <p><strong>Remarks:</strong></p>
                                <p id="view_remarks"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Case Modal -->
<div class="modal fade" id="approveCaseModal" tabindex="-1" role="dialog" aria-labelledby="approveCaseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveCaseModalLabel">Approve Household Case</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="approveCaseForm" action="household_case_records_approve.php" method="POST">
                <input type="hidden" name="case_id" id="approve_case_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approve_remarks">Remarks</label>
                        <textarea class="form-control" id="approve_remarks" name="remarks" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Approve Case</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Case Modal -->
<div class="modal fade" id="rejectCaseModal" tabindex="-1" role="dialog" aria-labelledby="rejectCaseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectCaseModalLabel">Reject Household Case</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectCaseForm" action="household_case_records_reject.php" method="POST">
                <input type="hidden" name="case_id" id="reject_case_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reject_remarks">Remarks</label>
                        <textarea class="form-control" id="reject_remarks" name="remarks" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Reject Case</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Case Modal -->
<div class="modal fade" id="editCaseModal" tabindex="-1" role="dialog" aria-labelledby="editCaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="editCaseModalLabel">Edit Household Case</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editCaseForm" action="household_case_records_update.php" method="POST">
                <input type="hidden" name="case_id" id="edit_case_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_beneficiary_name">Beneficiary Name</label>
                                <input type="text" class="form-control" id="edit_beneficiary_name" name="beneficiary_name" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_contact_number">Contact Number</label>
                                <input type="text" class="form-control" id="edit_contact_number" name="contact_number">
                            </div>
                            <div class="form-group">
                                <label for="edit_monthly_income">Monthly Income</label>
                                <input type="number" class="form-control" id="edit_monthly_income" name="monthly_income" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_address">Complete Address</label>
                                <textarea class="form-control" id="edit_address" name="address" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_problem">Problem Presented</label>
                        <textarea class="form-control" id="edit_problem" name="problem" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_remarks">Remarks</label>
                        <textarea class="form-control" id="edit_remarks" name="remarks" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View History Modal -->
<div class="modal fade" id="viewHistoryModal" tabindex="-1" role="dialog" aria-labelledby="viewHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title" id="viewHistoryModalLabel">Case History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="timeline">
                    <div id="history-timeline">
                        <!-- History items will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Page specific script -->
<script>
    // View Case Function
    function viewCase(id) {
        $.ajax({
            url: 'household_case_records_view.php',
            type: 'POST',
            data: {
                case_id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const case_data = response.data;
                    $('#view_date_submitted').text(new Date(case_data.date_created).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }));
                    $('#view_submitted_by').text(case_data.submitted_by_name);
                    $('#view_beneficiary_name').text(case_data.beneficiary_lastname + ', ' + case_data.beneficiary_firstname + ' ' + case_data.beneficiary_middlename);
                    $('#view_status').html(`<span class="status-badge status-${case_data.status.toLowerCase()}">${case_data.status}</span>`);
                    $('#view_contact_number').text(case_data.contact_number || 'N/A');
                    $('#view_monthly_income').text('₱' + parseFloat(case_data.estimated_monthly_income).toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    }));
                    $('#view_date_approved').text(case_data.date_approved ? new Date(case_data.date_approved).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) : 'N/A');
                    $('#view_approved_by').text(case_data.approved_by_name || 'N/A');
                    $('#view_address').text(case_data.complete_address);
                    $('#view_problem').text(case_data.problem_presented);
                    $('#view_remarks').text(case_data.remarks || 'No remarks');
                    $('#view_last_updated').text(new Date(case_data.date_updated).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }));
                    $('#view_case_number').text(case_data.case_number);
                    $('#viewCaseModal').modal('show');
                } else {
                    alert('Error loading case details');
                }
            },
            error: function() {
                alert('Error loading case details');
            }
        });
    }

    // Approve Case Function
    function approveCase(id) {
        $('#approve_case_id').val(id);
        $('#approveCaseModal').modal('show');
    }

    // Reject Case Function
    function rejectCase(id) {
        $('#reject_case_id').val(id);
        $('#rejectCaseModal').modal('show');
    }

    // Edit Case Function
    function editCase(id) {
        $.ajax({
            url: 'household_case_records_view.php',
            type: 'POST',
            data: {
                case_id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const case_data = response.data;
                    $('#edit_case_id').val(id);
                    $('#edit_beneficiary_name').val(case_data.beneficiary_lastname + ', ' + case_data.beneficiary_firstname + ' ' + case_data.beneficiary_middlename);
                    $('#edit_contact_number').val(case_data.contact_number);
                    $('#edit_monthly_income').val(case_data.estimated_monthly_income);
                    $('#edit_address').val(case_data.complete_address);
                    $('#edit_problem').val(case_data.problem_presented);
                    $('#edit_remarks').val(case_data.remarks);
                    $('#editCaseModal').modal('show');
                }
            }
        });
    }

    // View History Function
    function viewHistory(id) {
        $.ajax({
            url: 'household_case_records_history.php',
            type: 'POST',
            data: {
                case_id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const history = response.history;
                    let timelineHtml = '';

                    history.forEach(function(item) {
                        timelineHtml += `
                        <div class="time-label">
                            <span class="bg-secondary">${item.date}</span>
                        </div>
                        <div>
                            <i class="fas fa-edit bg-primary"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> ${item.time}</span>
                                <h3 class="timeline-header">${item.action}</h3>
                                <div class="timeline-body">
                                    ${item.details}
                                </div>
                                <div class="timeline-footer">
                                    By: ${item.user}
                                </div>
                            </div>
                        </div>
                    `;
                    });

                    $('#history-timeline').html(timelineHtml);
                    $('#viewHistoryModal').modal('show');
                }
            }
        });
    }

    // Handle form submission
    $('#editCaseForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Case updated successfully');
                    location.reload();
                } else {
                    alert('Error updating case: ' + response.message);
                }
            }
        });
    });

    // Form Validation
    $(document).ready(function() {
        // Initialize DataTable
        $("#household-table").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#household-table_wrapper .col-md-6:eq(0)');

        $('#approveCaseForm, #rejectCaseForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            alert(result.message);
                            location.reload();
                        } else {
                            alert(result.message || 'Error processing request');
                        }
                    } catch (e) {
                        alert('Error processing request');
                    }
                },
                error: function() {
                    alert('Error processing request');
                }
            });
        });
    });
</script>

<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .timeline-header {
        margin: 0;
        font-size: 1.1em;
        font-weight: bold;
    }

    .timeline-body {
        margin: 10px 0;
    }

    .timeline-footer {
        font-size: 0.9em;
        color: #6c757d;
    }

    .time-label {
        position: relative;
        margin-bottom: 20px;
    }

    .time-label span {
        padding: 5px 10px;
        color: white;
        border-radius: 4px;
    }

    .card {
        margin-bottom: 20px;
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
    }

    .card-header {
        background-color: rgba(0, 0, 0, .03);
        border-bottom: 1px solid rgba(0, 0, 0, .125);
        padding: .75rem 1.25rem;
    }

    .card-title {
        margin-bottom: 0;
        font-size: 1.1rem;
        font-weight: 400;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.85em;
        font-weight: bold;
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
</style>
</body>

</html>