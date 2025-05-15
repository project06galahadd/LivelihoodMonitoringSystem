<?php
include 'includes/session.php';

if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status']; // 'ARCHIVED' or 'ACTIVE'

    // Update the status in the database
    $sql = "UPDATE tbl_items SET STATUS='$status' WHERE id='$id'";

    if ($conn->query($sql)) {
        echo "Status updated successfully";
    } else {
        echo "Failed to update status";
    }
} else {
    echo "Missing parameters";
}
