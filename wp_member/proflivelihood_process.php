<?php
include "includes/session.php";



$MEMID=$_POST['MEMID'];
$PROF_DESCRIPTION=$_POST['PROF_DESCRIPTION'];

    $allowTypes = array('jpg','JPG','png','PNG','jpeg','JPEG','gif','GIF'); 
    $fileNames = array_filter($_FILES['PROF_LIVELIHOOD']['name']); 
    if(!empty($fileNames)){ 
        foreach($_FILES['PROF_LIVELIHOOD']['name'] as $key=>$val){ 
          
            $fileName = basename($_FILES['PROF_LIVELIHOOD']['name'][$key]); 
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
         
            if(in_array($fileType, $allowTypes)){ 
					 $FILE_TEMP=$_FILES["PROF_LIVELIHOOD"]["tmp_name"][$key]; 
                     $FILE_UPLOAD = addslashes(file_get_contents($FILE_TEMP)); 
					 
                    $sql="INSERT INTO `tbl_records`(`MEMID`, `PROF_DESCRIPTION`, `PROF_LIVELIHOOD`, `DATE_SUBMITTED`) 
					VALUES ('$MEMID','".$PROF_DESCRIPTION[$key]."','".$FILE_UPLOAD."','".date('Y-m-d')."')";
					if($conn->query($sql)){
						echo '<script>
						Swal.fire({
						icon: "success",
						title: "Successfully",
						text: "Your file has been successfully submited and  waiting for the confirmation.",
						showConfirmButton: false,
						timer: 3000
						});
					</script>';
					}else{
						echo '<script>
						Swal.fire({
						icon: "error",
						title: "Oops...",
						text: "Opps! we have error while saving your file",
						showConfirmButton: false,
						timer: 3000
						});
					</script>';
					}
                
            }else{ 
                echo '<script>
            Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.",
            showConfirmButton: false,
            timer: 3000
        });
        </script>';
            } 
        }
         
    }else{ 
        $statusMsg = 'Please select a file to upload.'; 
		echo '<script>
        Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Please select a file to upload.",
        showConfirmButton: false,
        timer: 3000
    });
    </script>';
    } 
?>