<?php
	include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'lgu.php?home=localgovermentunits';
	}

	if(isset($_POST['submit'])){
		$BRGY_ID 		= $_POST['BRGY_ID'];
		$BRGY_NAME   	=$conn->real_escape_string(strtoupper($_POST['BRGY_NAME']));
		$BRGY_CAPTAIN   	=$_POST['BRGY_CAPTAIN'];
		$BRGY_CONTACT   	=$_POST['BRGY_CONTACT'];
		$BRGY_UPDATEBY	=$addedby;
		$BRGY_LASTUPDATE	=date('Y-m-d h:i:s A');
		
		$sql="UPDATE tbl_barangay
		SET BRGY_NAME='$BRGY_NAME',BRGY_CAPTAIN='$BRGY_CAPTAIN', BRGY_CONTACT='$BRGY_CONTACT', BRGY_UPDATEBY='$BRGY_UPDATEBY',BRGY_LASTUPDATE='$BRGY_LASTUPDATE'
		WHERE BRGY_ID = '$BRGY_ID'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Barangay name updated successfully';
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