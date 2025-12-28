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
    header("Location: ../users/index.php?error=self_follow");
    exit;
}


$check = mysqli_query($conn, "SELECT * FROM followers WHERE follower='$follower' AND following='$following'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO followers (follower, following) VALUES ('$follower', '$following')");
}

mysqli_close($conn);
header("Location: ../users/index.php");
exit;
?>
