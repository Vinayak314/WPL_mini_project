<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Code from submit_query.php (No login required, handles form POST)
    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    $sql = "INSERT INTO queries (name, email, reason) VALUES ('$name', '$email', '$reason')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Message sent successfully!'); window.location.href='../contact.html';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location.href='../contact.html';</script>";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Code from fetch_queries.php (Admin only)
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['member_id'])) {
        echo json_encode(['error' => 'not_logged_in']);
        exit();
    }

    if ($_SESSION['role'] !== 'admin') {
        echo json_encode(['error' => 'not_authorized']);
        exit();
    }

    $name = $_SESSION['name'];

    $sql = "SELECT * FROM queries ORDER BY submitted_at DESC";
    $result = mysqli_query($conn, $sql);

    $queries = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $queries[] = $row;
    }

    echo json_encode(['name' => $name, 'queries' => $queries]);
}

mysqli_close($conn);
?>
