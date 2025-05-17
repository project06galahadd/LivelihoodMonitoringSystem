<!-- View Modal -->
<div class="modal fade" id="view">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View Household Case</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Household Head:</strong> <span id="view_household_head"></span></p>
                        <p><strong>Case Type:</strong> <span id="view_case_type"></span></p>
                        <p><strong>Address:</strong> <span id="view_address"></span></p>
                        <p><strong>Contact Number:</strong> <span id="view_contact"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Family Size:</strong> <span id="view_family_size"></span></p>
                        <p><strong>Monthly Income:</strong> <span id="view_monthly_income"></span></p>
                        <p><strong>Status:</strong> <span id="view_status"></span></p>
                        <p><strong>Remarks:</strong> <span id="view_remarks"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <p><strong>Case Details:</strong></p>
                        <p id="view_case_details"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approve">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Approve Household Case</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="record_id" class="recordid">
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea class="form-control" name="remarks" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="approve" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="reject">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reject Household Case</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="record_id" class="recordid">
                    <div class="form-group">
                        <label>Reason for Rejection</label>
                        <textarea class="form-control" name="remarks" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="reject" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div> 