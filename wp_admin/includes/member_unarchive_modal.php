<div class="modal fade" id="member_unarchive_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="fa fa-undo"></span> UNARCHIVE MEMBER</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <form action="member_unarchive.php" method="POST">
                    <input type="hidden" id="unarchive_memid" name="MEMID">
                    <strong>Are you sure you want to unarchive this member?</strong><br>
                    <span id="unarchive_membername"></span>
            </div>
            <div class="modal-footer">
                <button type="submit" name="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-check"></i> Confirm
                </button>
            </div>
            </form>
        </div>
    </div>
</div>