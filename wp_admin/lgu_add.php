<?php
	include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'lgu.php?home=localgovermentunits';
	}

	if(isset($_POST['submit'])){			
		$BRGY_NAME   =$conn->real_escape_string(strtoupper($_POST['BRGY_NAME']));
		$BRGY_CAPTAIN   =$conn->real_escape_string(strtoupper($_POST['BRGY_CAPTAIN']));
		$BRGY_CONTACT   =$_POST['BRGY_CONTACT'];
		$BRGY_CREATED	=date('Y-m-d h:i:s A');
		$BRGY_ADDEDBY =$addedby;

		$sql= "INSERT INTO tbl_barangay(BRGY_NAME,BRGY_CAPTAIN,BRGY_CONTACT,BRGY_CREATED,BRGY_ADDEDBY)VALUES('$BRGY_NAME','$BRGY_CAPTAIN','$BRGY_CONTACT','$BRGY_CREATED','$BRGY_ADDEDBY')";
		if($conn->query($sql)){
			$_SESSION['success'] = 'New barangay created successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	

	}	
	else{
		$_SESSION['error'] = 'Fill up add form first';
		
	}
	header('location:'.$return);
?>