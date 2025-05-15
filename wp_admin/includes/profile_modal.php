<!-- Add -->
<div class="modal fade" id="profile">
    <div class="modal-dialog">
        <div class="modal-content">
          	<div class="modal-header">
			<h4 class="modal-title"><span class="fa fa-edit"></span> EDIT</h4>
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
              		<span aria-hidden="true">&times;</span></button>
          	</div>
          	<div class="modal-body">
            	<form class="form-horizontal" method="POST" action="profile_update.php?return=<?php echo basename($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
				      <div class="row">
				        <div class="col-md-12">
                <div class="form-group">
                <input type="hidden" class="form-control" name="ID" value="<?php echo $user['ID']; ?>">
                    <label for="photo" class="control-label">Photo:</label>
                    <input class="form-control" name="image" type="file" id="formFile" onchange="preview()"><br>
                   <img id="frame" src="" class="img-fluid " style="border-radius:10px">
                </div>
                </div>
               
               </div>
          	</div>
          	<div class="modal-footer">
            	<button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> CLOSE</button>
            	<button type="submit" class="btn bg-maroon btn-sm" name="upload"><i class="fa fa-check-square-o"></i> SUBMIT</button>
            	</form>
          	</div>
        </div>
    </div>
</div>

<div class="modal fade" id="logout">
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">LOGOUT</h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
      <p>Are you sure you want to logout now?</p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fas fa-close"></i> CANCEL</button>
      <a href="logout.php" class="btn btn-primary btn-sm"><i class="fa fa-check-square-o"></i> PROCEED</a>
    </div>

    </div>
    <!-- /.modal-content -->
  </div>
<!-- /.modal-dialog -->
</div>

<div class="modal fade" id="editProfile">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"><span class="fa fa-edit"></span> EDIT</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="form-horizontal" method="POST" action="edit_profile_update.php?return=<?php echo basename($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
            <div class="modal-body">
            <div class="row"> 
          		<div class="col-sm-6">  
              <div class="form-group">
                  	<label for="username" class="control-label">USERNAME</label>
                    	<input type="text" class="form-control" name="USERNAME" value="<?php echo $user['USERNAME']; ?>">
                  	</div>
                </div>
                <div class="col-sm-6"> 
                <div class="form-group">
                    <label for="password" class="control-label">PASSWORD</label>
                      <input type="password" class="form-control" name="PASSWORD" value="<?php echo $user['PASSWORD']; ?>">
                    </div>
                </div>
                <div class="col-sm-5">
                <div class="form-group">
                  	<label for="firstname" class="control-label">FIRST NAME</label>
                    	<input type="text" class="form-control" name="FIRSTNAME" value="<?php echo $user['FIRSTNAME']; ?>">
                  	</div>
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                  	<label for="lastname" class="control-label">M.I</label>
                    	<input type="text" class="form-control" name="MI" value="<?php echo $user['MI']; ?>">
                  	</div>
                </div>
                <div class="col-sm-5">
                <div class="form-group">
                  	<label for="lastname" class="control-label">LAST NAME</label>
                    	<input type="text" class="form-control" name="LASTNAME" value="<?php echo $user['LASTNAME']; ?>">
                  	</div>
                </div>
                <div class="col-sm-12">
                <div class="form-group">
                    <label for="curr_password" class="control-label">CURRENT PASSWORD <i> input current password to save changes</i></label>
                      <input type="password" class="form-control" name="curr_password" placeholder="input current password to save changes" required>
                    </div>
                </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> CANCEL</button>
              <button type="submit" class="btn btn-primary btn-sm" name="save"><i class="fa fa-check-square-o"></i> PROCEED</button>
            </div>
			  </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->  