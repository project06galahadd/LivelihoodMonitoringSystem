<!---FOR ARCHIVE---->
<div class="modal fade" id="archive">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="fa fa-archive"></span> Archive Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="archive_actions.php" method="POST" class="archive-form" novalidate>
                    <input type="hidden" class="record-id" name="id" required>
                    <input type="hidden" class="record-type" name="type" required>
                    <div class="alert alert-warning">
                        You are about to archive: <strong><span class="record-name"></span></strong>
                        <br>This action can be reversed later.
                    </div>
                    <div class="form-group mt-3">
                        <label>Status Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="STATUS_REMARKS" placeholder="Please provide a reason for archiving..." 
                            required minlength="5" maxlength="255"></textarea>
                        <div class="invalid-feedback">Please provide a valid reason (minimum 5 characters).</div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="submit" name="submit" class="btn btn-warning btn-sm">
                    <i class="fa fa-archive"></i> Archive
                </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!---FOR UNARCHIVE---->
<div class="modal fade" id="unarchive">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="fa fa-folder-open"></span> Unarchive Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="unarchive_actions.php" method="GET" class="unarchive-form" novalidate>
                    <input type="hidden" class="record-id" name="id" required>
                    <input type="hidden" class="record-type" name="type" required>
                    <div class="alert alert-info">
                        You are about to unarchive: <strong><span class="record-name"></span></strong>
                        <br>This will restore the record to active status.
                    </div>
                    <div class="form-group mt-3">
                        <label>Status Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="STATUS_REMARKS" placeholder="Please provide a reason for unarchiving..." 
                            required minlength="5" maxlength="255"></textarea>
                        <div class="invalid-feedback">Please provide a valid reason (minimum 5 characters).</div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="submit" name="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-folder-open"></i> Unarchive
                </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Archive/Unarchive Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" role="dialog" aria-labelledby="archiveModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title" id="archiveModalLabel">Confirm Action</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="archiveModalText">Are you sure you want to archive this item?</p>
        <input type="hidden" id="archive_id">
        <input type="hidden" id="archive_type">
        <input type="hidden" id="archive_status">
        <input type="hidden" id="archive_return_url">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" id="archiveConfirmBtn">Confirm</button>
      </div>
    </div>
  </div>
</div>
<script>
// Function to validate form
function validateForm(form) {
    var isValid = form.checkValidity();
    if (!isValid) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.classList.add('was-validated');
    return isValid;
}

function openArchiveModal(id, type, name, status, returnUrl) {
    if (!id || !type || !name) {
        alert('Error: Missing required parameters');
        return;
    }

    // Select the appropriate modal based on status
    var modal = status === 'ARCHIVED' ? $('#unarchive') : $('#archive');
    
    // Reset form validation
    modal.find('form').removeClass('was-validated');
    modal.find('textarea').val('');
    
    // Set the form values
    modal.find('.record-id').val(id);
    modal.find('.record-type').val(type);
    modal.find('.record-name').text(name);
    
    // Update modal title based on type
    var title = type.charAt(0).toUpperCase() + type.slice(1);
    modal.find('.modal-title').html(
        '<span class="fa ' + (status === 'ARCHIVED' ? 'fa-folder-open' : 'fa-archive') + '"></span> ' + 
        (status === 'ARCHIVED' ? 'Unarchive ' : 'Archive ') + title
    );
    
    // Show the modal
    modal.modal('show');
}

// Add form validation handlers
$(document).ready(function() {
    $('.archive-form, .unarchive-form').on('submit', function(event) {
        return validateForm(this);
    });

    // Clear validation on modal close
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form').removeClass('was-validated');
        $(this).find('textarea').val('');
    });

    // Prevent closing modal when clicking outside if form is filled
    $('.modal').on('hide.bs.modal', function(event) {
        var textarea = $(this).find('textarea');
        if (textarea.val().trim().length > 0) {
            if (!confirm('Are you sure you want to close? Any entered data will be lost.')) {
                event.preventDefault();
            }
        }
    });

    // Handle confirm
    $('#archiveConfirmBtn').on('click', function() {
        var id = $('#archive_id').val();
        var type = $('#archive_type').val();
        var status = $('#archive_status').val();
        var returnUrl = $('#archive_return_url').val();
        var newStatus = (status === 'ARCHIVED') ? 'unarchive' : 'archive';
        $.ajax({
            url: 'archive_actions.php',
            type: 'POST',
            data: {
                ARCHIVE_ID: id,
                ARCHIVE_TYPE: type,
                ARCHIVE_STATUS: newStatus,
                return_url: returnUrl
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#archiveModal').modal('hide');
                    // Remove row or reload
                    if (response.removeRowId) {
                        $('#row_' + response.removeRowId).fadeOut(400, function() { $(this).remove(); });
                    } else {
                        location.href = returnUrl;
                    }
                } else {
                    alert(response.message || 'Failed to update status.');
                }
            },
            error: function() {
                alert('Failed to update status.');
            }
        });
    });
});
</script>
<!-- End Archive/Unarchive Modal -->