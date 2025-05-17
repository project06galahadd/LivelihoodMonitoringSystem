<?php
include 'includes/session.php';
include 'includes/conn.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];
    
    $sql = "SELECT * FROM tbl_household_case_records WHERE record_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
    echo json_encode($row);
    }
    else {
        echo json_encode(['error' => 'Record not found']);
    }
}
?> 