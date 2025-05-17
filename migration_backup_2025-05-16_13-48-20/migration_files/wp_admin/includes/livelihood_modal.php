<!---FOR ADD---->
<div class="modal fade" id="add">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          	<div class="modal-header">
			  <h4 class="modal-title"><span class="fa fa-plus"></span> NEW LIVELIHOOD PROGRAM</h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
          	<div class="modal-body">
            	<form autocomplete="off" class="form-horizontal needs-validation" method="POST" action="livelihood_add.php" enctype="multipart/form-data" novalidate>
          		  <div class="row">
                    <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">PROGRAM</label>
                            <input type="text" class="form-control" name="LIVELIHOOD_NAME" required>
                            <div class="invalid-feedback">
                               Livelihood name is required*
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">DESCRIPTION</label>
                            <input type="text" class="form-control" name="LIVELIHOOD_DESCRIPTION" required>
                            <div class="invalid-feedback">
                               Description is required*
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
<div class="modal fade" id="sector_edit_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          	<div class="modal-header">
			  <h4 class="modal-title"><span class="fa fa-edit"></span> PROGRAM </h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
          	<div class="modal-body">
            	<form class="form-horizontal needs-validation" method="POST" action="livelihood_edit.php" enctype="multipart/form-data" novalidate>
          		  <div class="row">
                    <input type="hidden" id="EDIT_LIVELIHOOD_ID" name="LIVELIHOOD_ID" required>
                    <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">PROGRAM NAME</label>
                             <input type="text" class="form-control" id="EDIT_LIVELIHOOD_NAME" name="LIVELIHOOD_NAME" required>
                             
                            <div class="invalid-feedback">
                                Program is required*
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                       <div class="form-group">
                             <label for="lastname" class="control-label">DESCRIPTION</label>
                            <input type="text" class="form-control" id="EDIT_LIVELIHOOD_DESCRIPTION" name="LIVELIHOOD_DESCRIPTION" required>
                            <div class="invalid-feedback">
                               Description is required*
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
<!---FOR DELETE---->
<div class="modal fade" id="sector_del_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><span class="fa fa-question-circle"></span> Are you sure you want to delete this sector?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="livelihood_delete.php" method="POST">
                 <input type="hidden" id="DEL_SUBSEC_ID" name="LIVELIHOOD_ID">
                  NAME : <span id="DEL_SUBSEC_NAME"></span><br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CANCEL</button>
                <button type="submit" name="submit" class="btn btn-danger btn-sm"><i class="fa fa-thrash"></i> <span class="fa fa-trash"></span>  SUBMIT</button>
            </div>
            </form>
        </div>
    </div>
</div>


