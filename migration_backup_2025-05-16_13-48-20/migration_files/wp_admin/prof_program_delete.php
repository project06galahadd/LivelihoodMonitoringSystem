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
        $sql="DELETE FROM tbl_records WHERE RECID='$RECID'";
        if($conn ->query($sql)){
            $_SESSION['success'] ="Records has been successfully deleted";
        }else{
            $_SESSION['error'] ="No record deleted!";
        }
    }else{
        $_SESSION['error'] ="Please select first the record to delete";
    }
    header('location:'.$return);
?>