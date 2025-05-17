<?php
    $host ="localhost";
    $user="root";
    $pass="";

    $conn=new mysqli($host,$user,$pass);
    if($conn->connect_error){
        echo 'success';
    }

    $sql="SHOW DATABASES";
    $query=$conn->query($sql);
    $db=array();
    while($rowdb=$query->fetch_row()){
        $db[]=$rowdb[0];
    }
    print implode('<br>',$db);
?>