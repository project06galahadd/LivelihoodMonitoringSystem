<?php
// Set headers for JSON response
header('Content-Type: application/json');

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // Turn off error display
ini_set('log_errors', 1); // Enable error logging

require_once "wp_admin/includes/conn.php";

function respond($status, $message, $extra = []) {
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

// Check if database connection is successful
if (!$conn) {
    respond('error', 'Database connection failed');
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond('error', 'Invalid request method');
}

try {
    // Required fields validation
    $required_fields = [
        'USERNAME', 'EMAIL', 'PASSWORD', 'CONFIRM_PASSWORD',
        'FIRSTNAME', 'LASTNAME', 'GENDER', 'DATE_OF_BIRTH',
        'MOBILE', 'BARANGAY', 'ADDRESS', 'EDUCATIONAL_BACKGROUND',
        'EMPLOYMENT_HISTORY', 'SKILLS_QUALIFICATION',
        'DESIRED_LIVELIHOOD_PROGRAM', 'EXP_LIVELIHOOD_PROGRAM_CHOSEN',
        'CURRENT_LIVELIHOOD_SITUATION', 'REQUIRED_TRAINING',
        'REASON_INTERESTED_IN_LIVELIHOOD', 'VALID_ID_NUMBER'
    ];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            respond('error', "$field is required");
        }
    }

    // Validate username length
    if (strlen($_POST['USERNAME']) < 6) {
        respond('error', 'Username must be at least 6 characters long');
    }

    // Validate email format
    if (!filter_var($_POST['EMAIL'], FILTER_VALIDATE_EMAIL)) {
        respond('error', 'Invalid email format');
    }

    // Validate password
    if (strlen($_POST['PASSWORD']) < 8) {
        respond('error', 'Password must be at least 8 characters long');
    }
    if (!preg_match('/[0-9]/', $_POST['PASSWORD']) || !preg_match('/[a-zA-Z]/', $_POST['PASSWORD'])) {
        respond('error', 'Password must contain both letters and numbers');
    }
    if ($_POST['PASSWORD'] !== $_POST['CONFIRM_PASSWORD']) {
        respond('error', 'Passwords do not match');
    }

    // Validate AGE if present
    if (isset($_POST['AGE']) && (!is_numeric($_POST['AGE']) || $_POST['AGE'] < 0 || $_POST['AGE'] > 120)) {
        respond('error', 'Invalid age value');
    }

    // Check if username/email already exists in tbl_users
    $username = $conn->real_escape_string($_POST['USERNAME']);
    $email = $conn->real_escape_string($_POST['EMAIL']);
    $checkUser = $conn->query("SELECT id FROM tbl_users WHERE username = '$username' OR email = '$email'");
    if ($checkUser && $checkUser->num_rows > 0) {
        respond('error', 'Username or email already exists');
    }
    if ($checkUser) $checkUser->free();

    // Check for duplicate AUTO_NUMBER if provided
    $auto_number = isset($_POST['AUTO_NUMBER']) ? $conn->real_escape_string($_POST['AUTO_NUMBER']) : '';
    if ($auto_number !== '') {
        $checkAuto = $conn->query("SELECT AUTO_NUMBER FROM tbl_autonumber WHERE AUTO_NUMBER = '$auto_number'");
        if ($checkAuto && $checkAuto->num_rows > 0) {
            respond('error', 'Duplicate AUTO_NUMBER');
        }
        if ($checkAuto) $checkAuto->free();
    }

    // Validate file uploads before transaction
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if (!isset($_FILES['UPLOAD_ID']) || $_FILES['UPLOAD_ID']['error'] !== 0) {
        respond('error', 'Valid ID file is required');
    }
    $idFile = $_FILES['UPLOAD_ID'];
    if ($idFile['size'] > $maxSize) {
        respond('error', 'ID file size exceeds 2MB limit');
    }
    $idExt = strtolower(pathinfo($idFile['name'], PATHINFO_EXTENSION));
    if (!in_array($idExt, $allowedTypes)) {
        respond('error', 'ID file must be JPG, JPEG, PNG, or GIF');
    }

    if (!isset($_FILES['UPLOAD_WITH_SELFIE']) || $_FILES['UPLOAD_WITH_SELFIE']['error'] !== 0) {
        respond('error', 'Selfie with ID is required');
    }
    $selfieFile = $_FILES['UPLOAD_WITH_SELFIE'];
    if ($selfieFile['size'] > $maxSize) {
        respond('error', 'Selfie file size exceeds 2MB limit');
    }
    $selfieExt = strtolower(pathinfo($selfieFile['name'], PATHINFO_EXTENSION));
    if (!in_array($selfieExt, $allowedTypes)) {
        respond('error', 'Selfie file must be JPG, JPEG, PNG, or GIF');
    }

    // Hash the password
    $password = password_hash($_POST['PASSWORD'], PASSWORD_DEFAULT);

    // Start transaction
    $conn->begin_transaction();

    // Insert into tbl_users
    $role = 'MEMBER';
    $stmtUser = $conn->prepare("INSERT INTO tbl_users (username, email, password, role, ACC_STATUS) VALUES (?, ?, ?, ?, 1)");
    if (!$stmtUser) {
        $conn->rollback();
        respond('error', 'Database error (users): ' . $conn->error);
    }
    $stmtUser->bind_param('ssss', $username, $email, $password, $role);
    if (!$stmtUser->execute()) {
        $stmtUser->close();
        $conn->rollback();
        respond('error', 'Error creating user: ' . $stmtUser->error);
    }
    $user_id = $stmtUser->insert_id;
    $stmtUser->close();

    // Prepare data for tbl_members
    $data = [
        'FIRSTNAME' => strtoupper(trim($_POST['FIRSTNAME'])),
        'LASTNAME' => strtoupper(trim($_POST['LASTNAME'])),
        'MIDDLENAME' => strtoupper(trim($_POST['MIDDLENAME'] ?? '')),
        'GENDER' => strtoupper(trim($_POST['GENDER'])),
        'DATE_OF_BIRTH' => trim($_POST['DATE_OF_BIRTH']),
        'AGE' => trim($_POST['AGE']),
        'MOBILE' => strtoupper(trim($_POST['MOBILE'])),
        'BARANGAY' => strtoupper(trim($_POST['BARANGAY'])),
        'ADDRESS' => strtoupper(trim($_POST['ADDRESS'])),
        'EDUCATIONAL_BACKGROUND' => strtoupper(trim($_POST['EDUCATIONAL_BACKGROUND'])),
        'EMPLOYMENT_HISTORY' => strtoupper(trim($_POST['EMPLOYMENT_HISTORY'])),
        'SKILLS_QUALIFICATION' => strtoupper(trim($_POST['SKILLS_QUALIFICATION'])),
        'DESIRED_LIVELIHOOD_PROGRAM' => strtoupper(trim($_POST['DESIRED_LIVELIHOOD_PROGRAM'])),
        'EXP_LIVELIHOOD_PROGRAM_CHOSEN' => strtoupper(trim($_POST['EXP_LIVELIHOOD_PROGRAM_CHOSEN'])),
        'CURRENT_LIVELIHOOD_SITUATION' => strtoupper(trim($_POST['CURRENT_LIVELIHOOD_SITUATION'])),
        'REQUIRED_TRAINING' => strtoupper(trim($_POST['REQUIRED_TRAINING'])),
        'REASON_INTERESTED_IN_LIVELIHOOD' => strtoupper(trim($_POST['REASON_INTERESTED_IN_LIVELIHOOD'])),
        'VALID_ID_NUMBER' => strtoupper(trim($_POST['VALID_ID_NUMBER'])),
        'AUTO_NUMBER' => $auto_number
    ];
    foreach ($data as $key => $value) {
        $data[$key] = $conn->real_escape_string($value);
    }

    // Read file contents
    $idContent = file_get_contents($idFile['tmp_name']);
    $selfieContent = file_get_contents($selfieFile['tmp_name']);
    $profileContent = $selfieContent; // Use selfie as profile picture
    $statusRemarks = "PENDING";

    // Prepare the SQL statement with all fields explicitly listed
    $sql = "INSERT INTO tbl_members (
        user_id, EMAIL, FIRSTNAME, MIDDLENAME, LASTNAME, GENDER, DATE_OF_BIRTH, AGE, 
        MOBILE, BARANGAY, ADDRESS, EDUCATIONAL_BACKGROUND, EMPLOYMENT_HISTORY, 
        SKILLS_QUALIFICATION, DESIRED_LIVELIHOOD_PROGRAM, EXP_LIVELIHOOD_PROGRAM_CHOSEN, 
        CURRENT_LIVELIHOOD_SITUATION, REQUIRED_TRAINING, REASON_INTERESTED_IN_LIVELIHOOD, 
        VALID_ID_NUMBER, UPLOAD_ID, UPLOAD_WITH_SELFIE, PROFILE, STATUS_REMARKS, 
        DATE_OF_APPLICATION, RECORD_NUMBER
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?
    )";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $conn->rollback();
        respond('error', 'Database error (members): ' . $conn->error);
    }

    $stmt->bind_param(
        'isssssssssssssssssssssss',
        $user_id, $email, $data['FIRSTNAME'], $data['MIDDLENAME'], $data['LASTNAME'],
        $data['GENDER'], $data['DATE_OF_BIRTH'], $data['AGE'], $data['MOBILE'],
        $data['BARANGAY'], $data['ADDRESS'], $data['EDUCATIONAL_BACKGROUND'],
        $data['EMPLOYMENT_HISTORY'], $data['SKILLS_QUALIFICATION'],
        $data['DESIRED_LIVELIHOOD_PROGRAM'], $data['EXP_LIVELIHOOD_PROGRAM_CHOSEN'],
        $data['CURRENT_LIVELIHOOD_SITUATION'], $data['REQUIRED_TRAINING'],
        $data['REASON_INTERESTED_IN_LIVELIHOOD'], $data['VALID_ID_NUMBER'],
        $idContent, $selfieContent, $profileContent, $statusRemarks, $data['AUTO_NUMBER']
    );

    if ($stmt->execute()) {
        // Save AUTO_NUMBER if provided
        if ($auto_number !== '') {
            $conn->query("INSERT INTO tbl_autonumber (AUTO_NUMBER) VALUES ('$auto_number')");
        }
        $stmt->close();
        $conn->commit();
        $_SESSION['registration_success'] = "Your application has been successfully submitted and is waiting for approval.";
        respond('success', 'Registration successful', ['redirect' => 'wp_member/home.php']);
    } else {
        $stmt->close();
        $conn->rollback();
        respond('error', 'Error saving member: ' . $stmt->error);
    }

} catch (Exception $e) {
    if ($conn->in_transaction) $conn->rollback();
    error_log("Registration error: " . $e->getMessage());
    respond('error', 'An error occurred during registration. Please try again.');
} finally {
    if (isset($stmt) && $stmt) $stmt->close();
    if (isset($stmtUser) && $stmtUser) $stmtUser->close();
    $conn->close();
}
