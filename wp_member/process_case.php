<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Temporary test code - remove after testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Temporary user ID for testing
}

require_once "../wp_admin/includes/conn.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Debug: Log request method and data
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . print_r($_POST, true));
error_log("GET Data: " . print_r($_GET, true));

// Handle POST request (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required_fields = [
            'beneficiary_lastname', 'beneficiary_firstname', 'beneficiary_relationship',
            'age', 'birth_date', 'marital_status', 'educational_attainment',
            'occupation', 'complete_address', 'sitio_purok', 'barangay',
            'town', 'province', 'estimated_monthly_income', 'problem_presented'
        ];

        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Prepare the SQL statement
        $sql = "INSERT INTO tbl_household_case_records (
            submitted_by, beneficiary_lastname, beneficiary_firstname, beneficiary_middlename,
            beneficiary_relationship, age, birth_date, marital_status, educational_attainment,
            occupation, complete_address, sitio_purok, barangay, town, province,
            contact_number, estimated_monthly_income, problem_presented, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING')";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }

        $stmt->bind_param(
            "isssssisssssssssds",
            $_SESSION['user_id'],
            $_POST['beneficiary_lastname'],
            $_POST['beneficiary_firstname'],
            $_POST['beneficiary_middlename'],
            $_POST['beneficiary_relationship'],
            $_POST['age'],
            $_POST['birth_date'],
            $_POST['marital_status'],
            $_POST['educational_attainment'],
            $_POST['occupation'],
            $_POST['complete_address'],
            $_POST['sitio_purok'],
            $_POST['barangay'],
            $_POST['town'],
            $_POST['province'],
            $_POST['contact_number'],
            $_POST['estimated_monthly_income'],
            $_POST['problem_presented']
        );

        if (!$stmt->execute()) {
            throw new Exception("Database execute error: " . $stmt->error);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Case record submitted successfully'
        ]);

    } catch (Exception $e) {
        error_log("Error in process_case.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
}
// Handle GET request (view/edit form)
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // If ID is provided, get the case details for editing
    if (isset($_GET['id'])) {
        try {
            $stmt = $conn->prepare("SELECT * FROM tbl_household_case_records WHERE id = ? AND submitted_by = ?");
            if (!$stmt) {
                throw new Exception("Database prepare error: " . $conn->error);
            }
            
            $stmt->bind_param("ii", $_GET['id'], $_SESSION['user_id']);
            if (!$stmt->execute()) {
                throw new Exception("Database execute error: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("Record not found");
            }
            
            $record = $result->fetch_assoc();
            
            // Display the edit form
            include 'case_form.php';
            
        } catch (Exception $e) {
            error_log("Error in process_case.php: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conn)) {
                $conn->close();
            }
        }
    } else {
        // Display the new case form
        include 'case_form.php';
    }
}
else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?> 