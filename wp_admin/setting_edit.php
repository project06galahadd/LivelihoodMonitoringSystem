<?php
	include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'setting.php?home=setting';
	}

	if(isset($_POST['form-system-update'])){			
		$SYS_ID			=$_POST['SYS_ID'];
		$SYS_NAME		=$_POST['SYS_NAME'];
		$SYS_ADDRESS	=$_POST['SYS_ADDRESS'];
		$SYS_EMAIL		=$_POST['SYS_EMAIL'];
		$SYS_ABOUT		=$conn->real_escape_string($_POST['SYS_ABOUT']);
		$r1				=$_POST['r1'];

		$insert = $conn->query("UPDATE tbl_system_setting SET SYS_NAME='$SYS_NAME', SYS_ADDRESS='$SYS_ADDRESS',SYS_EMAIL='$SYS_EMAIL', SYS_ABOUT='$SYS_ABOUT',SYS_ISDEFAULT='$r1' WHERE SYS_ID='$SYS_ID'");
		if($insert){ 
			$_SESSION['success'] = "Updated uploaded successfully."; 
		}else{ 
			$_SESSION['error'] = "File upload failed, please try again."; 
		}  

	}else{
		$_SESSION['error'] = 'Fill up add form first';
		
	}
	//header('location: clientAccount.php?q='.$_POST['id']);
	header('location:'.$return);
?>