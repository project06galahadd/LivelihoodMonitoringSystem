<?php
	include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'notes.php';
	}

	if(isset($_POST['submit'])){
		$ID 		= $_POST['id'];
		$TITLE     	=$_POST['title'];
        $DESCRIPTION=$_POST['description'];
        $DATE   	=$_POST['start_datetime'];
		$TIME		=$_POST['end_datetime'];

		$sql="UPDATE `schedule_list` 
		SET `title`='$TITLE',
		`description`='$DESCRIPTION',
		`start_datetime`='$DATE',
		`end_datetime`='$TIME' WHERE id = '$ID'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Event updated successfully';
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