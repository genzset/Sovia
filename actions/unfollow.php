<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "database");

if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

$follower = $_SESSION['username'];
$following = $_GET['user'];


if ($follower === $following) {
    header("Location: ../users/index.php?error=self_unfollow");
    exit;
}

mysqli_query($conn, "DELETE FROM followers WHERE follower='$follower' AND following='$following'");
mysqli_close($conn);
header("Location: ../users/index.php");
exit;
?>
