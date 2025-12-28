<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$conn = mysqli_connect("localhost", "root", "", "database");
$id = (int)$_GET['id'];
mysqli_query($conn, "DELETE FROM comments WHERE status_id=$id");
mysqli_query($conn, "DELETE FROM statuses WHERE id=$id");
mysqli_close($conn);
header("Location: ../admin/admin.php");
?>