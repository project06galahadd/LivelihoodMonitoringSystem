<?php
	session_start();
	$conn = new mysqli('localhost', 'root', '', 'livelihood_database');
	
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	if(isset($_POST['login'])){
		$USERNAME = $_POST['USERNAME'];
		$PASSWORD = $_POST['PASSWORD'];

		$sql1 = "SELECT * FROM (SELECT te.ID AS UID, te.USERNAME, te.PASSWORD, te.ACC_STATUS, te.ROLE FROM tbl_users te) t WHERE t.USERNAME = '$USERNAME' AND t.ACC_STATUS=1";
		$query1 = $conn->query($sql1);
		if($query1->num_rows > 0){
			$row = $query1->fetch_assoc();
			if($PASSWORD==$row['PASSWORD']){
				$_SESSION['admin'] = $row['UID'];	
				if($row['ROLE']=="ADMIN"){
					header('location:home.php?dashboard=home');
				}elseif($row['ROLE']=="USER"){
					header('location:home.php?dashboard=home');
				}else{
					header('location: signin.php?error=error');
				}
			}else{
				$_SESSION['error'] = 'Incorrect password';
				header('location: signin.php?error=error');
			}
		}else{
			header('location: signin.php?error=error');
			$_SESSION['error'] = 'username or password not found!';
		}
		
	}else{
		$_SESSION['error'] = 'Input admin credentials first';
		header('location: signin.php?error=error');
	}
?>