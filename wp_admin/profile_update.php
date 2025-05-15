<?php 
// Include the database configuration file  
require_once 'includes/session.php'; 
 
if(isset($_GET['return'])){
    $return = $_GET['return'];
    
}
else{
    $return = 'home.php';
}

if(isset($_POST["upload"])){ 
    //$_SESSION['error']='error while uploading!!'; 

    $ID =$_POST['ID'];
    if(!empty($_FILES["image"]["name"])) { 
        // Get file info 
        $fileName = basename($_FILES["image"]["name"]); 
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
         
        // Allow certain file formats 
        $allowTypes = array('jpg','png','jpeg','gif'); 
        if(in_array($fileType, $allowTypes)){ 
            $image = $_FILES['image']['tmp_name']; 
            $imgContent = addslashes(file_get_contents($image)); 
         
            // Insert image content into database 
            $insert = $conn->query("UPDATE tbl_users SET PROFILE='$imgContent' WHERE ID='$ID'"); 
             
            if($insert){ 
                $_SESSION['success'] = "File uploaded successfully."; 
            }else{ 
                $_SESSION['error'] = "File upload failed, please try again."; 
            }  
        }else{ 
            $_SESSION['error'] = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.'; 
        } 
    }else{ 
        $_SESSION['error'] = 'Please select an image file to upload.'; 
    } 
} 
 
// Display status message 
header('location:'.$return);
?>