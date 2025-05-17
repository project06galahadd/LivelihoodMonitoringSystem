<!---FOR ARCHIVE---->
<div class="modal fade" id="member_archive_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><span class="fa fa-archive"></span> ARCHIVE MEMBER</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <form action="member_archive.php" method="POST">
                    <input type="hidden" id="archive_memid" name="MEMID">
                    <strong>Are you sure you want to archive this member?</strong><br>
                    <span id="archive_membername" class="text-danger font-weight-bold"></span><br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> CANCEL</button>
                <button type="submit" name="submit" class="btn btn-warning btn-sm"><i class="fa fa-archive"></i> ARCHIVE</button>
            </div>
            </form>
        </div>
    </div>
</div>