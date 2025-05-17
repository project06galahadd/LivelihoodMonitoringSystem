<?php
    include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'prof_program.php?home=proflivelihood';
	}

    if(isset($_POST['submit'])){
        $RECID=$_POST['RECID'];
        $PROF_STATUS=$_POST['PROF_STATUS'];
        $PROF_REMARKS=$_POST['PROF_REMARKS'];

        $sql="UPDATE tbl_records SET PROF_STATUS='$PROF_STATUS', PROF_REMARKS='$PROF_REMARKS', PROF_UPDATED='".date('Y-m-d')."' WHERE RECID='$RECID'";
        if($conn ->query($sql)){
            $_SESSION['success'] ="Submitted file has been successfully updated";
        }else{
            $_SESSION['error'] ="No record update!";
        }
    }else{
        $_SESSION['error'] ="Please select first the record to delete";
    }
    header('location:'.$return);
?>