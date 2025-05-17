<?php
include 'includes/session.php';

// Set default return page
$return = isset($_GET['return']) ? $_GET['return'] : 'member.php?home=members';

if (isset($_POST['submit'])) {
    $MEMID = $_POST['MEMID'];
    $STATUS_REMARKS = mysqli_real_escape_string($conn, $_POST['STATUS_REMARKS']); // Prevent SQL injection

    $sql = "UPDATE tbl_members 
                SET STATUS = 'ARCHIVED', STATUS_REMARKS = '$STATUS_REMARKS' 
                WHERE MEMID = '$MEMID'";

    if ($conn->query($sql)) {
        $_SESSION['success'] = "Member has been archived successfully.";
    } else {
        $_SESSION['error'] = "Failed to archive member. Please try again.";
    }
} else if (isset($_POST['unarchive'])) {
    $MEMID = $_POST['MEMID'];
    $sql = "UPDATE tbl_members SET STATUS = 'ACTIVE', STATUS_REMARKS = 'ACTIVE' WHERE MEMID = '$MEMID'";
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Member has been unarchived successfully.";
    } else {
        $_SESSION['error'] = "Failed to unarchive member. Please try again.";
    }
} else {
    $_SESSION['error'] = "Please select a record to archive or unarchive.";
}

header('location:' . $return);
