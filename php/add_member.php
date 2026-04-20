<?php
session_start();
header('Content-Type: application/json');
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit();
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'not_authorized']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate required fields
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['error' => 'missing_fields', 'message' => 'Please fill all fields.']);
        exit();
    }

    // Check for duplicate email
    $check = mysqli_query($conn, "SELECT member_id FROM team_members WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['error' => 'duplicate_email', 'message' => 'A member with this email already exists.']);
        exit();
    }

    $sql = "INSERT INTO team_members (name, email, password, role) VALUES ('$name', '$email', '$password', 'member')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Member added successfully!']);
    } else {
        echo json_encode(['error' => 'db_error', 'message' => 'Failed to add member: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['error' => 'invalid_method']);
}

mysqli_close($conn);
?>
