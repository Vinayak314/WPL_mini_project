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

$sql = "SELECT member_id, name, email, role FROM team_members WHERE role = 'member' ORDER BY name ASC";
$result = mysqli_query($conn, $sql);

$members = array();
while ($row = mysqli_fetch_assoc($result)) {
    $members[] = $row;
}

echo json_encode(['members' => $members]);

mysqli_close($conn);
?>
