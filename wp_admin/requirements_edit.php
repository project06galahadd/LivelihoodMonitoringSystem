<?php
	include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'requirements.php?home=requirements';
	}

	if(isset($_POST['submit'])){
		$REQ_ID 		=$_POST['REQ_ID'];
		$REQ_NAME   	=$conn->real_escape_string(strtoupper($_POST['REQ_NAME']));
		$sql="UPDATE tbl_requirements SET REQ_NAME='$REQ_NAME' WHERE REQ_ID = '$REQ_ID'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Requirements updated successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}

	}
	else{
		$_SESSION['error'] = 'Select recird to edit first';
	}

	header('location:'.$return);
?>