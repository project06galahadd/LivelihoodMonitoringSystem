<?php
$host = 'localhost';
$user = 'root'; // change if not using default
$pass = '';     // change if you have a password set
$db   = 'livelihood_database'; // <-- replace this

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
