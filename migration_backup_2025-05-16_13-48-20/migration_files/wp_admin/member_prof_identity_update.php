<?php
	include 'includes/session.php';
    if(isset($_GET['return'])){
		  $return = $_GET['return'];
	  }else{
		$return = 'member_info.php?member_info='.$_POST['member_info'].'&record='.$_POST['record'].'&name='.$_POST['name'];
	  }

	if(isset($_POST['register'])){			
      $member_info= $_POST['member_info'];
      // $record= $_POST['record'];
      // $name= $_POST['name'];
      $VALID_ID_NUMBER=$_POST['VALID_ID_NUMBER'];
		
			$UPLOAD_IDS = basename($_FILES["UPLOAD_ID"]["name"]); 
			$UPLOAD_SIZE = $_FILES["UPLOAD_ID"]["size"]; 
			$UPLOAD_ID_TYPE = pathinfo($UPLOAD_IDS, PATHINFO_EXTENSION); 

			$UPLOAD_WITH_SELFIES = basename($_FILES["UPLOAD_WITH_SELFIE"]["name"]); 
			$UPLOAD_WITH_SELFIE_SIZE =$_FILES["UPLOAD_WITH_SELFIE"]["size"]; 
			$UPLOAD_WITH_SELFIE_TYPE = pathinfo($UPLOAD_WITH_SELFIES, PATHINFO_EXTENSION); 
			$allowTypes = array('jpg','JPG','png','PNG','jpeg','JPEG','gif','GIF'); 
			if($UPLOAD_SIZE <=60971521 || $UPLOAD_WITH_SELFIE_SIZE <=60971521){

				if(in_array($UPLOAD_ID_TYPE, $allowTypes) || in_array($UPLOAD_WITH_SELFIE_TYPE, $allowTypes)){ 
					$IMAGE_ID = $_FILES['UPLOAD_ID']['tmp_name']; 
					$UPLOAD_ID = addslashes(file_get_contents($IMAGE_ID)); 

					$WITH_SELFIE = $_FILES['UPLOAD_WITH_SELFIE']['tmp_name']; 
					$UPLOAD_WITH_SELFIE = addslashes(file_get_contents($WITH_SELFIE)); 

          if(empty($_FILES["UPLOAD_ID"]["name"]) && empty($_FILES["UPLOAD_WITH_SELFIE"]["name"])){
            $sql= "UPDATE tbl_members SET
            VALID_ID_NUMBER='$VALID_ID_NUMBER'
            WHERE ID='$ID'";
            if($conn->query($sql)){
              $_SESSION['success']='Successfully updated';
            }else{
              $_SESSION['error']='Opps! we have error while saving your information';
            }

          }elseif(empty($_FILES["UPLOAD_ID"]["name"])){
            $sql= "UPDATE tbl_members SET
            VALID_ID_NUMBER='$VALID_ID_NUMBER',
            UPLOAD_WITH_SELFIE='$UPLOAD_WITH_SELFIE'
            WHERE MEMID='$member_info'";
            if($conn->query($sql)){
              $_SESSION['success']='Successfully updated';
            }else{
              $_SESSION['error']='Opps! we have error while saving your information';
            }
          }elseif(empty($_FILES["UPLOAD_WITH_SELFIE"]["name"])){
            $sql= "UPDATE tbl_members SET
            VALID_ID_NUMBER='$VALID_ID_NUMBER',
            UPLOAD_ID='$UPLOAD_ID'
            WHERE MEMID='$member_info'";
            if($conn->query($sql)){
              $_SESSION['success']='Successfully updated';
            }else{
              $_SESSION['error']='Opps! we have error while saving your information';
            }
          }else{
            $sql= "UPDATE tbl_members SET
            VALID_ID_NUMBER='$VALID_ID_NUMBER',
            UPLOAD_ID='$UPLOAD_ID',
            UPLOAD_WITH_SELFIE='$UPLOAD_WITH_SELFIE'
            WHERE MEMID='$member_info'";
            if($conn->query($sql)){
              $_SESSION['success']='Successfully updated';
            }else{
              $_SESSION['error']='Opps! we have error while saving your information';
            }
          }

				}else{ 
					$_SESSION['error']='Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
				} 
			}else{
				$_SESSION['error']='Attachment image is to large to save!. Please choose smaller size.';
			}
		
	}else{
		$_SESSION['error'] = 'Fill up add form first';
	}
	header('location:'.$return);
?>