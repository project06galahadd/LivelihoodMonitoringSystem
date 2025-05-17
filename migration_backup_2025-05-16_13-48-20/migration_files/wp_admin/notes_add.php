<?php
	include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'notes.php';
	}

	if(isset($_POST['submit'])){			
		$TITLE=$_POST['title'];
		$DESCRIPTION=$_POST['description'];
		$DATE=$_POST['start_datetime'];
		$TIME=$_POST['end_datetime'];

		$sql= "INSERT INTO schedule_list(title, description, start_datetime, end_datetime)VALUES('$TITLE','$DESCRIPTION','$DATE','$TIME')";
		if($conn->query($sql)){
			$_SESSION['success'] = 'New Event have been created.';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	

	}	
	else{
		$_SESSION['error'] = 'Fill up add form first';
		
	}
	//header('location: clientAccount.php?q='.$_POST['id']);
	header('location:'.$return);
?>