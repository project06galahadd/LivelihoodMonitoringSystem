<!---FOR ADD---->
<div class="modal fade" id="add">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          	<div class="modal-header">
			  <h4 class="modal-title"><span class="fa fa-plus"></span> BARANGAY</h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
          	<div class="modal-body">
            	<form autocomplete="off" class="form-horizontal needs-validation" method="POST" action="lgu_add.php" enctype="multipart/form-data" novalidate>
          		  <div class="row">
                    <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">NAME OF BARANGAY</label>
                            <input type="text" class="form-control" name="BRGY_NAME" required>
                            <div class="invalid-feedback">
                                Brgy name is required*
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">NAME OF BARANGAY CAPTAIN</label>
                            <input type="text" class="form-control" name="BRGY_CAPTAIN" required>
                            <div class="invalid-feedback">
                                Brgy name is required*
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">CONTACT NUMBER OF BARANGAY</label>
                            <input type="text" class="form-control" name="BRGY_CONTACT" required>
                            <div class="invalid-feedback">
                                Brgy name is required*
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
<div class="modal fade" id="lgu_edit_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          	<div class="modal-header">
			  <h4 class="modal-title"><span class="fa fa-edit"></span> BARANGAY </h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
          	<div class="modal-body">
            	<form autocomplete="off" class="form-horizontal needs-validation" method="POST" action="lgu_edit.php" enctype="multipart/form-data" novalidate>
          		  <div class="row">
                    
                  <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">BRGY NAME</label>
                             <input type="hidden" class="form-control" id="EDIT_BRGY_ID" name="BRGY_ID" required>
                            <input type="text" class="form-control" id="EDIT_BRGY_NAME" name="BRGY_NAME" required>
                            <div class="invalid-feedback">
                                LGU name is required*
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">BRGY CAPTAIN</label>
                            <input type="text" class="form-control" id="EDIT_BRGY_CAPTAIN" name="BRGY_CAPTAIN" required>
                            <div class="invalid-feedback">
                                LGU name is required*
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">BRGY MOBILE</label>
                            
                            <input type="text" class="form-control" id="EDIT_BRGY_CONTACT" name="BRGY_CONTACT" required>
                            <div class="invalid-feedback">
                                LGU name is required*
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
<!-- Archive Modal -->
<div class="modal fade" id="archive_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Archive Record</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="archive_actions.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="ARCHIVE_ID" id="ARCHIVE_ID">
                    <input type="hidden" name="ARCHIVE_TYPE" id="ARCHIVE_TYPE">
                    <input type="hidden" name="ARCHIVE_STATUS" id="ARCHIVE_STATUS">
                    <input type="hidden" name="return_url" id="ARCHIVE_RETURN_URL">
                    <div class="text-center">
                        <h4>Are you sure you want to <span id="archive_action_text">archive</span> this <span id="archive_type_text">record</span>?</h4>
                        <h2 class="bold" id="ARCHIVE_NAME"></h2>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning" name="archive" id="archive_submit">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Update modal text based on action
    $('#archive_modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var status = button.data('status');
        var type = button.data('type');
        var id = button.data('id');
        var name = button.data('name');
        var returnUrl = button.data('return-url');
        var modal = $(this);
        
        // Set the form values
        modal.find('#ARCHIVE_ID').val(id);
        modal.find('#ARCHIVE_TYPE').val(type);
        modal.find('#ARCHIVE_STATUS').val(status);
        modal.find('#ARCHIVE_NAME').text(name);
        modal.find('#ARCHIVE_RETURN_URL').val(returnUrl);
        
        // Update the modal text and button
        if(status == 'archive') {
            modal.find('#archive_action_text').text('archive');
            modal.find('.modal-title').text('Archive ' + type.charAt(0).toUpperCase() + type.slice(1));
            modal.find('#archive_submit').removeClass('btn-success').addClass('btn-warning').text('Archive');
        } else {
            modal.find('#archive_action_text').text('unarchive');
            modal.find('.modal-title').text('Unarchive ' + type.charAt(0).toUpperCase() + type.slice(1));
            modal.find('#archive_submit').removeClass('btn-warning').addClass('btn-success').text('Unarchive');
        }
        
        // Update the type text
        var typeText = '';
        switch(type) {
            case 'beneficiary':
                typeText = 'beneficiary';
                break;
            case 'livelihood':
                typeText = 'livelihood program';
                break;
            case 'barangay':
                typeText = 'barangay';
                break;
            case 'proof':
                typeText = 'proof of program';
                break;
            default:
                typeText = 'record';
        }
        modal.find('#archive_type_text').text(typeText);
    });
});
</script>


