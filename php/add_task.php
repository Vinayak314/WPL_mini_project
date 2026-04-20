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
    $member_id   = intval($_POST['member_id']);
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);
    $deadline    = mysqli_real_escape_string($conn, $_POST['deadline']);
    $event_name  = mysqli_real_escape_string($conn, $_POST['event_name']);

    // Validate required fields
    if (empty($member_id) || empty($title) || empty($status) || empty($deadline)) {
        echo json_encode(['error' => 'missing_fields', 'message' => 'Please fill all required fields.']);
        exit();
    }

    // Validate status value
    $allowed_statuses = ['urgent', 'in_progress', 'completed'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['error' => 'invalid_status', 'message' => 'Invalid status value.']);
        exit();
    }

    $sql = "INSERT INTO tasks (member_id, title, description, status, deadline, event_name) 
            VALUES ($member_id, '$title', '$description', '$status', '$deadline', '$event_name')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Task assigned successfully!']);
    } else {
        echo json_encode(['error' => 'db_error', 'message' => 'Failed to assign task: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['error' => 'invalid_method']);
}

mysqli_close($conn);
?>
