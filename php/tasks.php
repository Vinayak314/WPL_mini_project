<?php
session_start();
header('Content-Type: application/json');
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $action = $_GET['action'] ?? 'fetch';

    if ($action === 'fetch_all') {
        // Code from fetch_all_tasks.php
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['error' => 'not_authorized']);
            exit();
        }
        
        $sql = "SELECT t.*, m.name AS member_name 
                FROM tasks t 
                JOIN team_members m ON t.member_id = m.member_id 
                ORDER BY t.deadline ASC";
        $result = mysqli_query($conn, $sql);

        $tasks = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $tasks[] = $row;
        }

        echo json_encode(['tasks' => $tasks]);

    } else {
        // Code from fetch_tasks.php (fetch member's own tasks)
        $member_id = $_SESSION['member_id'];
        $name = $_SESSION['name'];

        $sql = "SELECT * FROM tasks WHERE member_id = $member_id ORDER BY deadline ASC";
        $result = mysqli_query($conn, $sql);

        $tasks = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $tasks[] = $row;
        }

        echo json_encode(['name' => $name, 'tasks' => $tasks]);
    }

} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        // Code from add_task.php
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['error' => 'not_authorized']);
            exit();
        }

        $member_id   = intval($_POST['member_id']);
        $title       = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $status      = mysqli_real_escape_string($conn, $_POST['status']);
        $deadline    = mysqli_real_escape_string($conn, $_POST['deadline']);
        $event_name  = mysqli_real_escape_string($conn, $_POST['event_name']);

        if (empty($member_id) || empty($title) || empty($status) || empty($deadline)) {
            echo json_encode(['error' => 'missing_fields', 'message' => 'Please fill all required fields.']);
            exit();
        }

        $today = date('Y-m-d');
        if ($deadline <= $today) {
            echo json_encode(['error' => 'invalid_date', 'message' => 'Deadline must be a future date.']);
            exit();
        }

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
        echo json_encode(['error' => 'invalid_action']);
    }
} else {
    echo json_encode(['error' => 'invalid_method']);
}

mysqli_close($conn);
?>
