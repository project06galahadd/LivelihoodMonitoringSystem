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
        $sql="DELETE FROM tbl_members WHERE MEMID='$MEMID'";
        if($conn ->query($sql)){
            $_SESSION['success'] ="Members name has been successfully deleted";
        }else{
            $_SESSION['error'] ="No record deleted!";
        }
    }else{
        $_SESSION['error'] ="Please select first the record to delete";
    }
    header('location:'.$return);
?>