<?php
session_start();
require "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$tribe_id = $_POST['id'];
$user_id = $_SESSION['user_id'];


$result = mysqli_query($con, "SELECT role FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
$is_admin = isset($user['role']) && $user['role'] === 'admin';


if ($is_admin) {
    
    mysqli_query($con, "DELETE FROM tribes WHERE id='$tribe_id'");
} else {
    
    $query = mysqli_query($con, "SELECT * FROM tribes WHERE id='$tribe_id' AND created_by='$user_id'");
    if (mysqli_num_rows($query) > 0) {
        // Hapus tribe (akan cascade delete tribe_members dan statuses dengan tribe_id)
        mysqli_query($con, "DELETE FROM tribes WHERE id='$tribe_id'");
    }
}

header("Location: ../users/tribes.php");
exit;
?>

