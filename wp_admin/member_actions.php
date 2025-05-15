<?php
    include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'member.php?home=members';
	}

    if(isset($_POST['submit'])){
        $MEMID=$_POST['MEMID'];
        $STATUS=$_POST['STATUS'];
        $STATUS_REMARKS=$_POST['STATUS_REMARKS'];
        if (strtoupper($STATUS) == 'UNARCHIVE' || strtoupper($STATUS) == 'ACTIVE') {
            $STATUS = 'ACTIVE';
            $STATUS_REMARKS = 'ACTIVE';
        }
        $sql="UPDATE tbl_members SET STATUS='$STATUS', STATUS_REMARKS='$STATUS_REMARKS' WHERE MEMID='$MEMID'";
        if($conn ->query($sql)){
            $_SESSION['success'] ="Members name has been successfully updated";
        }else{
            $_SESSION['error'] ="No record update!";
        }
    }else{
        $_SESSION['error'] ="Please select first the record to delete";
    }
    header('location:'.$return);
?>