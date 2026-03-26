<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM team_members WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['member_id'] = $row['member_id'];
        $_SESSION['name']      = $row['name'];
        $_SESSION['role']      = $row['role'];

        if ($row['role'] === 'admin') {
            header("Location: ../admin_queries.html");
        } else {
            header("Location: ../tasks.html");
        }
        exit();
    } else {
        echo "<script>alert('Invalid email or password'); window.location.href='../login.html';</script>";
    }
}

mysqli_close($conn);
?>
