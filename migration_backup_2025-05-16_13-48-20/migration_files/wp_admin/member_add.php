<?php
	include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}else{
		$return = 'member.php?home=member';
	}

	if(isset($_POST['submit'])){			
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
		$DESIRED_LIVELIHOOD_PROGRAM = strtoupper($_POST["DESIRED_LIVELIHOOD_PROGRAM"]);
		$EXP_LIVELIHOOD_PROGRAM_CHOSEN = $conn -> real_escape_string(strtoupper($_POST["EXP_LIVELIHOOD_PROGRAM_CHOSEN"]));
		$CURRENT_LIVELIHOOD_SITUATION = $conn -> real_escape_string(strtoupper($_POST["CURRENT_LIVELIHOOD_SITUATION"]));
		$REQUIRED_TRAINING=$conn -> real_escape_string(strtoupper($_POST["REQUIRED_TRAINING"]));
		$REASON_INTERESTED_IN_LIVELIHOOD = $conn -> real_escape_string(strtoupper($_POST["REASON_INTERESTED_IN_LIVELIHOOD"]));
		$VALID_ID_NUMBER =  $conn -> real_escape_string(strtoupper($_POST["VALID_ID_NUMBER"]));
		$AUTO_NUMBER    =$_POST['AUTO_NUMBER'];
		
		$sql="SELECT * FROM tbl_members WHERE FIRSTNAME='$FIRSTNAME' AND LASTNAME='$LASTNAME'";
		$query=$conn->query($sql);
		if($query->num_rows>0){
			$_SESSION['error']='Sorry! you cant proceed to your application because you have the same lastname and firstname in our database. Please visit to the Barangay Office.';
		}else{
			
				$UPLOAD_IDS = basename($_FILES["UPLOAD_ID"]["name"]); 
				$UPLOAD_SIZE = $_FILES["UPLOAD_ID"]["size"]; 
				$UPLOAD_ID_TYPE = pathinfo($UPLOAD_IDS, PATHINFO_EXTENSION); 
		
				$UPLOAD_WITH_SELFIES = basename($_FILES["UPLOAD_WITH_SELFIE"]["name"]); 
				$UPLOAD_WITH_SELFIE_SIZE =$_FILES["UPLOAD_WITH_SELFIE"]["size"]; 
				$UPLOAD_WITH_SELFIE_TYPE = pathinfo($UPLOAD_WITH_SELFIES, PATHINFO_EXTENSION); 
				
				$uploadfile_now =array($UPLOAD_ID_TYPE, $UPLOAD_WITH_SELFIE_TYPE);
		
				if($UPLOAD_SIZE <=2097152 || $UPLOAD_WITH_SELFIE_SIZE <=2097152){
		
				$allowTypes = array('jpg','JPG','png','PNG','jpeg','JPEG','gif','GIF'); 
				if(in_array($UPLOAD_ID_TYPE, $allowTypes) || in_array($UPLOAD_WITH_SELFIE_TYPE, $allowTypes)){ 
					$IMAGE_ID = $_FILES['UPLOAD_ID']['tmp_name']; 
					$UPLOAD_ID = addslashes(file_get_contents($IMAGE_ID)); 
		
					$WITH_SELFIE = $_FILES['UPLOAD_WITH_SELFIE']['tmp_name']; 
					$UPLOAD_WITH_SELFIE = addslashes(file_get_contents($WITH_SELFIE)); 
					$sql="INSERT INTO `tbl_members`(`FIRSTNAME`, `MIDDLENAME`, `LASTNAME`, `GENDER`, `DATE_OF_BIRTH`, `AGE`, `MOBILE`, `BARANGAY`, `ADDRESS`, `EDUCATIONAL_BACKGROUND`, `EMPLOYMENT_HISTORY`, `SKILLS_QUALIFICATION`, `DESIRED_LIVELIHOOD_PROGRAM`, `EXP_LIVELIHOOD_PROGRAM_CHOSEN`, `CURRENT_LIVELIHOOD_SITUATION`, `REQUIRED_TRAINING`, `REASON_INTERESTED_IN_LIVELIHOOD`, `VALID_ID_NUMBER`, `UPLOAD_ID`, `UPLOAD_WITH_SELFIE`,`DATE_OF_APPLICATION`,RECORD_NUMBER) 
					VALUES ('$FIRSTNAME','$MIDDLENAME','$LASTNAME','$GENDER','$DATE_OF_BIRTH','$AGE ','$MOBILE','$BARANGAY','$ADDRESS','$EDUCATIONAL_BACKGROUND','$EMPLOYMENT_HISTORY','$SKILLS_QUALIFICATION','$DESIRED_LIVELIHOOD_PROGRAM','$EXP_LIVELIHOOD_PROGRAM_CHOSEN','$CURRENT_LIVELIHOOD_SITUATION','$REQUIRED_TRAINING', '$REASON_INTERESTED_IN_LIVELIHOOD','$VALID_ID_NUMBER','$UPLOAD_ID','$UPLOAD_WITH_SELFIE',NOW(),'$AUTO_NUMBER')";
					if($conn->query($sql)){
						$_SESSION['success']='Your application has been successfully submited and  waiting for the confirmation.';
						$autonum= "INSERT INTO `tbl_autonumber`(AUTO_NUMBER)VALUES ('$AUTO_NUMBER')";
								$conn->query($autonum);
					}else{
						$_SESSION['error']='Opps! we have error while saving your information';
					}
					
				}else{ 
					$_SESSION['error']='Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
				} 
			}else{
				$_SESSION['error']='Attachment image is to large to save!. Please choose smaller size.';
			}
		}
	

	}else{
		$_SESSION['error'] = 'Fill up add form first';
		
	}
	header('location:'.$return);
?>