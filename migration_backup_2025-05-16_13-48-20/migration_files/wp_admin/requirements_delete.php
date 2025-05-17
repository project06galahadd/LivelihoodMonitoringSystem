<?php
    include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'requirements.php?home=requirements';
	}

    if(isset($_POST['submit'])){
        $REQ_ID=$_POST['REQ_ID'];
        $sql="DELETE FROM tbl_requirements WHERE REQ_ID='$REQ_ID'";
        if($conn ->query($sql)){
            $_SESSION['success'] ="Requirements has been successfully deleted";
        }else{
            $_SESSION['error'] ="No record deleted!";
        }
    }else{
        $_SESSION['error'] ="Please select first the record to delete";
    }
    header('location:'.$return);
?>