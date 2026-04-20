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
    $member_id = intval($_POST['member_id']);

    if (empty($member_id)) {
        echo json_encode(['error' => 'missing_fields', 'message' => 'Member ID is required.']);
        exit();
    }

    // Prevent deleting admin accounts
    $check = mysqli_query($conn, "SELECT role FROM team_members WHERE member_id = $member_id");
    if (mysqli_num_rows($check) == 0) {
        echo json_encode(['error' => 'not_found', 'message' => 'Member not found.']);
        exit();
    }
    $row = mysqli_fetch_assoc($check);
    if ($row['role'] === 'admin') {
        echo json_encode(['error' => 'cannot_delete_admin', 'message' => 'Cannot delete an admin account.']);
        exit();
    }

    // Delete their tasks first (foreign key)
    mysqli_query($conn, "DELETE FROM tasks WHERE member_id = $member_id");

    // Delete the member
    $sql = "DELETE FROM team_members WHERE member_id = $member_id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Member removed successfully.']);
    } else {
        echo json_encode(['error' => 'db_error', 'message' => 'Failed to remove member: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['error' => 'invalid_method']);
}

mysqli_close($conn);
?>
