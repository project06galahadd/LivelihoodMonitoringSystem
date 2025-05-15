<?php
	include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'member.php?home=member';
	}

	if(isset($_POST['submit'])){
		$MEMID=$_POST['MEMID'];
		$FIRSTNAME = $conn -> real_escape_string(strtoupper($_POST['FIRSTNAME']));
		$MIDDLENAME = $conn -> real_escape_string(strtoupper($_POST["MIDDLENAME"]));
		$LASTNAME =$conn -> real_escape_string(strtoupper($_POST["LASTNAME"]));
		$GENDER = $conn -> real_escape_string(strtoupper($_POST["GENDER"]));
		$DATE_OF_BIRTH =$_POST["DATE_OF_BIRTH"];
		$AGE = $_POST["AGE"];
		$MOBILE = $conn -> real_escape_string(strtoupper($_POST["MOBILE"]));
		$BARANGAY = $conn -> real_escape_string(strtoupper($_POST["BARANGAY"]));
		$ADDRESS = $conn -> real_escape_string(strtoupper($_POST["ADDRESS"]));
		$EDUCATIONAL_BACKGROUND =  $conn -> real_escape_string(strtoupper($_POST["EDUCATIONAL_BACKGROUND"]));
		$EMPLOYMENT_HISTORY = $conn -> real_escape_string(strtoupper($_POST["EMPLOYMENT_HISTORY"]));
		$SKILLS_QUALIFICATION = $conn -> real_escape_string(strtoupper($_POST["SKILLS_QUALIFICATION"]));
		$DESIRED_LIVELIHOOD_PROGRAM = $conn -> real_escape_string(strtoupper($_POST["DESIRED_LIVELIHOOD_PROGRAM"]));
		$EXP_LIVELIHOOD_PROGRAM_CHOSEN = $conn -> real_escape_string(strtoupper($_POST["EXP_LIVELIHOOD_PROGRAM_CHOSEN"]));
		$CURRENT_LIVELIHOOD_SITUATION = $conn -> real_escape_string(strtoupper($_POST["CURRENT_LIVELIHOOD_SITUATION"]));
		$REQUIRED_TRAINING=$conn -> real_escape_string(strtoupper($_POST["REQUIRED_TRAINING"]));
		$REASON_INTERESTED_IN_LIVELIHOOD = $conn -> real_escape_string(strtoupper($_POST["REASON_INTERESTED_IN_LIVELIHOOD"]));

		$sql="UPDATE `tbl_members` SET 
		`FIRSTNAME`='$FIRSTNAME',
		`MIDDLENAME`='$MIDDLENAME',
		`LASTNAME`='$LASTNAME',
		`GENDER`='$GENDER',
		`DATE_OF_BIRTH`='$DATE_OF_BIRTH',
		`AGE`='$AGE',
		`MOBILE`='$MOBILE',
		`BARANGAY`='$BARANGAY',
		`ADDRESS`='$ADDRESS',
		`EDUCATIONAL_BACKGROUND`='$EDUCATIONAL_BACKGROUND',
		`EMPLOYMENT_HISTORY`='$EMPLOYMENT_HISTORY',
		`SKILLS_QUALIFICATION`='$SKILLS_QUALIFICATION',
		`DESIRED_LIVELIHOOD_PROGRAM`='$DESIRED_LIVELIHOOD_PROGRAM',
		`EXP_LIVELIHOOD_PROGRAM_CHOSEN`='$EXP_LIVELIHOOD_PROGRAM_CHOSEN',
		`CURRENT_LIVELIHOOD_SITUATION`='$CURRENT_LIVELIHOOD_SITUATION',
		`REQUIRED_TRAINING`='$REQUIRED_TRAINING',
		`REASON_INTERESTED_IN_LIVELIHOOD`='$REASON_INTERESTED_IN_LIVELIHOOD' 
		WHERE MEMID='$MEMID'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Member information has been updated successfully';
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