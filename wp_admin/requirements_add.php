<?php
	include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'requirements.php?home=requirements';
	}

	if(isset($_POST['submit'])){			
		$REQ_NAME=$_POST['REQ_NAME'];
		for($i=0;$i<count($REQ_NAME);$i++){
		$sql="INSERT INTO tbl_requirements(REQ_NAME)VALUES('".$REQ_NAME[$i]."')";
		if($conn->query($sql)){
			$_SESSION['success'] = "Requirements has been save";
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
		
	}
	
	}else{
		$_SESSION['error'] = 'Fill up add form first';
		
	}
	header('location:'.$return);
?>