<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Manila');
	include 'conn.php';

	if(!isset($_SESSION['admin']) || trim($_SESSION['admin']) == ''){
		echo '<div style="padding:2em;font-family:sans-serif;color:#b71c1c;background:#fff3f3;border:1px solid #fbb;border-radius:8px;margin:2em auto;max-width:500px;text-align:center;">'
			. '<h2>Not Logged In</h2>'
			. '<p>You must be logged in as <b>admin</b> to access this page.</p>'
			. '<a href="signin.php" style="color:#fff;background:#3949ab;padding:0.5em 1em;border-radius:4px;text-decoration:none;">Go to Admin Login</a>'
			. '</div>';
		exit();
	}
	
	$sql = "SELECT * FROM tbl_users WHERE ID = '".$_SESSION['admin']."'";
	$query = $conn->query($sql);
	if($query->num_rows >0){
		$user = $query->fetch_assoc();
		$addedby =$user['LASTNAME'].', '.$user['FIRSTNAME'].' '.$user['MI'];
	}else{
		echo '<div style="padding:2em;font-family:sans-serif;color:#b71c1c;background:#fff3f3;border:1px solid #fbb;border-radius:8px;margin:2em auto;max-width:500px;text-align:center;">'
			. '<h2>Session Error</h2>'
			. '<p>Admin user not found. Please log in again.</p>'
			. '<a href="signin.php" style="color:#fff;background:#3949ab;padding:0.5em 1em;border-radius:4px;text-decoration:none;">Go to Admin Login</a>'
			. '</div>';
		exit();
	}

?>