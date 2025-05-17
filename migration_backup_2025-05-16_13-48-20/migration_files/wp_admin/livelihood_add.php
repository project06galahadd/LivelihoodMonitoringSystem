<?php
session_start();
include "includes/conn.php";

if (isset($_POST['submit'])) {
	$name = $_POST['LIVELIHOOD_NAME'];
	$description = $_POST['LIVELIHOOD_DESCRIPTION'];
	$date = date('Y-m-d H:i:s');

	// Check if program name already exists
	$check_sql = "SELECT * FROM tbl_livelihood WHERE LIVELIHOOD_NAME = ?";
	$check_stmt = $conn->prepare($check_sql);
	$check_stmt->bind_param("s", $name);
	$check_stmt->execute();
	$result = $check_stmt->get_result();

	if ($result->num_rows > 0) {
		$_SESSION['error'] = 'Livelihood program already exists';
	} else {
		$sql = "INSERT INTO tbl_livelihood (LIVELIHOOD_NAME, LIVELIHOOD_DESCRIPTION, LIVELIHOOD_CREATED, STATUS) VALUES (?, ?, ?, 'ACTIVE')";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("sss", $name, $description, $date);

		if ($stmt->execute()) {
			$_SESSION['success'] = 'Livelihood program added successfully';
		} else {
			$_SESSION['error'] = 'Error adding livelihood program';
		}
	}
} else {
	$_SESSION['error'] = 'Fill up add form first';
}

header('location: livelihood.php');
?>