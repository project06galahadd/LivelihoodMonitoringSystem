<?php
include 'includes/session.php';

if (isset($_GET['return'])) {
    $return = $_GET['return'];
} else {
    $return = 'member.php?home=members';
}

if (isset($_POST['submit'])) {
    $MEMID = $_POST['MEMID'];
    $sql = "UPDATE tbl_members SET STATUS = 'ACTIVE', STATUS_REMARKS = 'ACTIVE' WHERE MEMID = '$MEMID'";
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Member successfully unarchived.";
    } else {
        $_SESSION['error'] = "Failed to unarchive member.";
    }
} else {
    $_SESSION['error'] = "Please select a member to unarchive.";
}
header('location:' . $return);
