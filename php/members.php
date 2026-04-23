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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Code from fetch_members.php
    $sql = "SELECT member_id, name, email, role FROM team_members WHERE role = 'member' ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);

    $members = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $members[] = $row;
    }

    echo json_encode(['members' => $members]);
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        // Code from add_member.php
        $name     = mysqli_real_escape_string($conn, $_POST['name']);
        $email    = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['error' => 'missing_fields', 'message' => 'Please fill all fields.']);
            exit();
        }

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
    } elseif ($action === 'delete') {
        // Code from delete_member.php
        $member_id = intval($_POST['member_id']);

        if (empty($member_id)) {
            echo json_encode(['error' => 'missing_fields', 'message' => 'Member ID is required.']);
            exit();
        }

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

        mysqli_query($conn, "DELETE FROM tasks WHERE member_id = $member_id");

        $sql = "DELETE FROM team_members WHERE member_id = $member_id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Member removed successfully.']);
        } else {
            echo json_encode(['error' => 'db_error', 'message' => 'Failed to remove member: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['error' => 'invalid_action']);
    }
} else {
    echo json_encode(['error' => 'invalid_method']);
}

mysqli_close($conn);
?>
