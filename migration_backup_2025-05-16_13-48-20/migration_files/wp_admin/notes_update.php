<?php 
require_once('includes/session.php');
if($_SERVER['REQUEST_METHOD'] !='POST'){
    $_SESSION['error']='Error to save the notes';
    $conn->close();
    exit;
}
extract($_POST);
$allday = isset($allday);

if(empty($id)){
    $sql = "INSERT INTO `schedule_list` (`title`,`description`,`start_datetime`,`end_datetime`,`teacherid`) VALUES ('$title','$description','$start_datetime','$end_datetime','".$_SESSION['admin']."')";
}else{
    $sql = "UPDATE `schedule_list` set `title` = '{$title}', `description` = '{$description}', `start_datetime` = '{$start_datetime}', `end_datetime` = '{$end_datetime}' where `id` = '{$id}'";
}
$save = $conn->query($sql);
if($save){
        $_SESSION['success']='Notes Successfully Saved';
}else{
        $_SESSION['error']=$conn->error;
}
$conn->close();
header("location:home.php?home=dashboard");
?>