<!---FOR ADD---->
<div class="modal fade" id="_add_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          	<div class="modal-header">
			  <h4 class="modal-title"><span class="fa fa-plus"></span> ADD REQUIREMENTS</h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
            <form class="form-horizontal submit-livelihood-validation" action="requirements_add.php" method="POST"  autocomplete="off" enctype="multipart/form-data" novalidate>
            <div class="modal-body">
          		  <div class="row main-form">
                    <div class="col-sm-12">
                    <label for="lastname" class="control-label font-weight-normal">REQUIREMENTS</label>
                       <div class="input-group">
                            <input type="text" class="form-control" name="REQ_NAME[]" required>
                              <div class="input-group-prepend">
                                <button type="button" class="btn btn-primary add-more-form"><i class="fa fa-plus"></i></button>
                            </div>       
                        </div>
                    </div>


                    </div><!----row-->
                    <div class="paste-new-forms"></div>

                
          	</div>
          	<div class="modal-footer">
            	<button type="button" class="btn btn-warning text-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CLOSE</button>
            	<button type="submit" name="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> SUBMIT</button>
            	</form>
          	</div>
        </div>
    </div>
</div>


<!---FOR ADD---->
<div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          	<div class="modal-header">
			  <h4 class="modal-title"><span class="fa fa-plus"></span> UPDATE REQUIREMENTS</h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
            <form class="form-horizontal submit-livelihood-validation" action="requirements_edit.php" method="POST"  autocomplete="off" enctype="multipart/form-data" novalidate>
            <div class="modal-body">
                <input type="hidden" id="edit_reqid" name="REQ_ID" required>
          		  <div class="row">
                    <div class="col-sm-12">
                    <label for="lastname" class="control-label font-weight-normal">REQUIREMENTS</label>
                      <input type="text" class="form-control" id="edit_reqname" name="REQ_NAME" required>    
                    </div>
                </div><!----row-->
          	</div>
          	<div class="modal-footer">
            	<button type="button" class="btn btn-warning text-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CLOSE</button>
            	<button type="submit" name="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> SUBMIT</button>
            	</form>
          	</div>
        </div>
    </div>
</div>


<div class="modal fade" id="del_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><span class="fa fa-trash-alt"></span> PLEASE CONFIRM</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="requirements_delete.php" method="POST">
            <div class="modal-body text-center">
                 <input type="hidden" id="del_reqid" name="REQ_ID">
                Are you sure you want to delete this requirements?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CANCEL</button>
                <button type="submit" name="submit" class="btn btn-danger btn-sm"><i class="fa fa-thrash"></i> <span class="fa fa-trash"></span>  SUBMIT</button>
            </div>
            </form>
        </div>
    </div>
</div>