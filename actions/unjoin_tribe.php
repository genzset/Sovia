<?php
session_start();
require "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$tribe_id = $_POST['tribe_id'];


mysqli_query($con, "DELETE FROM tribe_members WHERE user_id='$user_id' AND tribe_id='$tribe_id'");


if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '../users/tribe_detail.php') !== false) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: ../users/tribes.php");
}
exit;
?>
