<?php
session_start();
include "wp_admin/includes/conn.php";
if (!isset($_SERVER['HTTP_REFERER'])){
    header("location:404.php?404");
}else{
  
$FIRSTNAME = $conn -> real_escape_string(strtoupper($_POST['FIRSTNAME']));
$MIDDLENAME = $conn -> real_escape_string(strtoupper($_POST["MIDDLENAME"]));
$LASTNAME =$conn -> real_escape_string(strtoupper($_POST["LASTNAME"]));
$DATE_OF_BIRTH =$_POST["DATE_OF_BIRTH"];
$MOBILE = $conn -> real_escape_string(strtoupper($_POST["MOBILE"]));
$BARANGAY = $conn -> real_escape_string(strtoupper($_POST["BARANGAY"]));

$sql ="SELECT MEMID,FIRSTNAME,MIDDLENAME,LASTNAME,DATE_OF_BIRTH,MOBILE, BARANGAY FROM tbl_members
WHERE FIRSTNAME='$FIRSTNAME' AND MIDDLENAME='$MIDDLENAME' AND LASTNAME='$LASTNAME' AND DATE_OF_BIRTH='$DATE_OF_BIRTH' AND MOBILE='$MOBILE' AND BARANGAY='$BARANGAY'";
$query =$conn->query($sql);
if($query->num_rows >0){
   $row=$query->fetch_assoc();
      $_SESSION['admin'] = $row['MEMID'];
      $MEMNAME=$row['LASTNAME'].', '.$row['FIRSTNAME'];

      echo '<script>
            Swal.fire({
            icon: "success",
            title: "WELCOME",
            text: "'.$MEMNAME.'",
            showConfirmButton: false,
            timer: 3000
            }).then((result) => {
              if(result){
                window.location.href = "wp_member/home.php?profile='.str_replace(' ', '_', $MEMNAME).'"
              }
            });
        </script>
        ';
}else{
  echo '<script>
  Swal.fire({
  icon: "error",
  title: "Oops...",
  text: "Sorry! You have entered invalid information",
  showConfirmButton: false,
  timer: 3000
});
  </script>';
}
}
?>




