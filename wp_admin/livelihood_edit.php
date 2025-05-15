<?php
session_start();
include "includes/conn.php";

if (isset($_POST['submit'])) {
	$id = $_POST['LIVELIHOOD_ID'];
	$name = $_POST['LIVELIHOOD_NAME'];
	$description = $_POST['LIVELIHOOD_DESCRIPTION'];

	// Check if program name already exists (excluding current record)
	$check_sql = "SELECT * FROM tbl_livelihood WHERE LIVELIHOOD_NAME = ? AND LIVELIHOOD_ID != ?";
	$check_stmt = $conn->prepare($check_sql);
	$check_stmt->bind_param("si", $name, $id);
	$check_stmt->execute();
	$result = $check_stmt->get_result();

	if ($result->num_rows > 0) {
		$_SESSION['error'] = 'Livelihood program already exists';
	} else {
		$sql = "UPDATE tbl_livelihood SET LIVELIHOOD_NAME = ?, LIVELIHOOD_DESCRIPTION = ? WHERE LIVELIHOOD_ID = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ssi", $name, $description, $id);

		if ($stmt->execute()) {
			$_SESSION['success'] = 'Livelihood program updated successfully';
		} else {
			$_SESSION['error'] = 'Error updating livelihood program';
		}
	}
} else {
	$_SESSION['error'] = 'Fill up edit form first';
}

header('location: livelihood.php');
?>