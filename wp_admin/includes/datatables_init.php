<?php
// This file contains the DataTables initialization code
?>
<script>
$(document).ready(function() {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#household-table')) {
        $('#household-table').DataTable().destroy();
    }

    // Initialize DataTable
    $("#household-table").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#household-table_wrapper .col-md-6:eq(0)');
});
</script> 