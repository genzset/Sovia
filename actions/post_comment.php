<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$conn = mysqli_connect("localhost", "root", "", "database");
$user_id = $_SESSION['user_id'];
$status_id = (int)$_POST['status_id'];
$content = mysqli_real_escape_string($conn, $_POST['content']);
if (!empty($content)) {
    mysqli_query($conn, "INSERT INTO comments (status_id, user_id, content) VALUES ($status_id, $user_id, '$content')");
}
mysqli_close($conn);
header("Location: ../users/index.php");
?>