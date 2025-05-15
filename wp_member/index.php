<?php
	session_start();
	if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'MEMBER') {
		header('Location: /LivelihoodMonitoringSystem/wp_member/home.php');
		exit();
	} else {
		header('Location: /LivelihoodMonitoringSystem/wp_member/signin.php');
		exit();
	}
?>