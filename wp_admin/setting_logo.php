<?php
	include 'includes/session.php';
    if(isset($_GET['return'])){
		$return = $_GET['return'];
		
	}
	else{
		$return = 'setting.php?home=setting';
	}

	if(isset($_POST['form-logo-update'])){			
		$SYS_ID			=$_POST['SYS_ID'];


		if(!empty($_FILES["SYS_LOGO"]["name"])) { 
			// Get file info 
			$fileName = basename($_FILES["SYS_LOGO"]["name"]); 
			$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
			 
			// Allow certain file formats 
			$allowTypes = array('jpg','png','jpeg','gif','avif','tiff','eps','raw','indd'); 
			if(in_array($fileType, $allowTypes)){ 
				$image = $_FILES['SYS_LOGO']['tmp_name']; 
				$SYS_LOGO = addslashes(file_get_contents($image)); 
			 
				$insert = $conn->query("UPDATE tbl_system_setting SET SYS_LOGO='$SYS_LOGO' WHERE SYS_ID='$SYS_ID'");
				if($insert){ 
					$_SESSION['success'] = "Updated uploaded successfully."; 
				}else{ 
					$_SESSION['error'] = "File upload failed, please try again."; 
				}  
			}else{ 
				$_SESSION['error'] = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.'; 
			} 
		}else{ 
			$_SESSION['error'] = 'Please select an image file to upload.'; 
		} 
	

	}else{
		$_SESSION['error'] = 'Fill up add form first';
		
	}
	//header('location: clientAccount.php?q='.$_POST['id']);
	header('location:'.$return);
?>