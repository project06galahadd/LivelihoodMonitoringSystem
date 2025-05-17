

<div class="modal fade" id="prof_actions_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><span class="fa fa-alert-exclamation"></span> Please take Actions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="needs-validation" action="prof_program_actions.php" method="POST" novalidate>
            <div class="modal-body">
                 <input type="hidden" id="actions_recid" name="RECID">
                  <div class="row"> 
                      <div class="col-md-12">
                        <div class="form-group">
                        <label for="" class="control-label font-weight-normal">STATUS</label>
                        <select style="width:100%" class="form-control" name="PROF_STATUS" required>
                          <option id="actions_status" selected></option>
                          <option>PENDING</option>
                          <option>APPROVED</option>
                          <option>REJECTED</option>
                        </select>
                        </div>        
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                        <label for="" class="control-label font-weight-normal">REMARKS</label>
                              <textarea rows="4" name="PROF_REMARKS" id="actions_remarks" class="form-control" required></textarea>
                        </div>        
                      </div>

                  </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CANCEL</button>
                <button type="submit" name="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i>  SUBMIT</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="prof_del_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><span class="fa fa-trash-alt"></span> PLEASE CONFIRM</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="prof_program_delete.php" method="POST">
            <div class="modal-body text-center">
                
                 <input type="hidden" id="del_recid" name="RECID">
                 <p> Are you sure you want to delete this record from your database? there's no undo?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CANCEL</button>
                <button type="submit" name="submit" class="btn btn-danger btn-sm"><i class="fa fa-thrash"></i> SUBMIT</button>
            </div>
            </form>
        </div>
    </div>
</div>