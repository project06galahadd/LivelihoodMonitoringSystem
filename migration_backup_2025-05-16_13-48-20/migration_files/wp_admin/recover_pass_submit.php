<?php
	include 'includes/session.php';

	if(isset($_POST['login'])){
		$userid = $_POST['id'];
		$newpassword = $_POST['NEWPASSWORD'];
		
		$sql = "UPDATE tbl_users SET PASSWORD = '$newpassword' WHERE ID = '$userid'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Password successfully changed!';
			header('location:recover_success.php?success=successfully_saved');
		}else{
			$_SESSION['error'] = $conn->error;
			header('location:recover-password.php?recover-password');
		}

	}
	else{
		$_SESSION['error'] = 'error!';
		header('location:recover-password.php?recover-password');
	}
?>