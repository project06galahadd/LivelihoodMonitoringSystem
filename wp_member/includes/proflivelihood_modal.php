<!---FOR ADD---->
<div class="modal fade" id="_add_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><span class="fa fa-plus"></span> SUBMIT PROOF OF LIVELIHOOD</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal submit-livelihood-validation" autocomplete="off" enctype="multipart/form-data" novalidate>
        <div class="modal-body">
          <input type="text" id="MEMID" name="MEMID" required hidden>
          <div class="row main-form">
            <div class="col-lg-4">
              <div class="form-group">
                <label class="font-weight-normal">LIVELIHOOD IMAGE</label>
                <input type="file" name="PROF_LIVELIHOOD[]" class="form-control" required multiple>
              </div>
            </div>

            <div class="col-sm-8">
              <label for="lastname" class="control-label font-weight-normal">DESCRIPION</label>
              <div class="input-group">
                <input type="text" class="form-control" name="PROF_DESCRIPTION[]" required>
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
          <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> SUBMIT</button>
      </form>
    </div>
  </div>
</div>
</div>
<!---FOR EDIT---->
<div class="modal fade" id="subattributes_edit_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><span class="fa fa-edit"></span> EDIT </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal needs-validation" method="POST" action="attributes_subdesc_edit.php?attributeid=<?= $_GET['attributeid']; ?>&attributename=<?= $_GET['attributename']; ?>&subsecname=<?= $_GET['subsecname']; ?>&lguid=<?= $_GET['lguid']; ?>&lguname=<?= $lguname; ?>" enctype="multipart/form-data" novalidate>
          <div class="row">
            <input type="hidden" id="EDIT_SUBATTRI_ID" name="SUBATTRI_ID" required>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="lastname" class="control-label">SUB DESCRIPION</label>
                <input type="text" class="form-control" id="EDIT_SUB_ATTRI_DESCRIPTION" name="SUB_ATTRI_DESCRIPTION" placeholder="SUB DESCRIPTION" required>
                <div class="invalid-feedback">
                  Sub description is required*
                </div>
              </div>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                <label for="lastname" class="control-label">YEAR</label>
                <input type="number" class="form-control" id="EDIT_SUB_ATTRI_YEAR" name="SUB_ATTRI_YEAR" value="<?= date('Y') ?>" placeholder="YEAR" required>
                <div class="invalid-feedback">
                  Year is required*
                </div>
              </div>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                <label for="lastname" class="control-label">FEMALE <i class="text-danger text-sm"></i></label>
                <input type="number" class="form-control" id="EDIT_SUB_ATTRI_FEMALE" placeholder="FEMALE" name="SUB_ATTRI_FEMALE">
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <label for="lastname" class="control-label">MALE <i class="text-danger text-sm"></i></label>
                <input type="number" class="form-control" id="EDIT_SUB_ATTRI_MALE" placeholder="MALE" name="SUB_ATTRI_MALE">
              </div>
            </div>


          </div><!----row-->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CLOSE</button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> SUBMIT</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!---FOR DELETE---->
<div class="modal fade" id="deleteSub_del_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class="fa fa-question"></span> Please confirm</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="attributes_subdesc_delete.php?attributeid=<?= $_GET['attributeid']; ?>&attributename=<?= $_GET['attributename']; ?>&subsecname=<?= $_GET['subsecname']; ?>&lguid=<?= $_GET['lguid']; ?>&lguname=<?= $lguname; ?>" method="POST">
          <input type="hidden" id="DEL_SUBATTRI_ID" name="SUBATTRI_ID">
          <span>Are you sure you want to delete this sector?</span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CANCEL</button>
        <button type="submit" name="submit" class="btn btn-danger btn-sm"><i class="fa fa-thrash"></i> <span class="fa fa-trash"></span> SUBMIT</button>
      </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="view_form_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Notification!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="view_form"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Okay</button>
      </div>
    </div>
  </div>
</div>