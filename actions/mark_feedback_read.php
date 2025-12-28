<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}


$conn = mysqli_connect("localhost", "root", "", "database");
$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT role FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
if (!isset($user['role']) || $user['role'] !== 'admin') {
    header("Location: ../users/index.php");
    exit;
}

$feedback_id = isset($_POST['feedback_id']) ? intval($_POST['feedback_id']) : 0;

if ($feedback_id > 0) {
    mysqli_query($conn, "UPDATE feedback SET status='read' WHERE id='$feedback_id'");
}

mysqli_close($conn);
header("Location: ../admin/view_feedback.php");
exit;
?>

