<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "database");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

$post_id = $_POST['id'];
$user_id = $_SESSION['user_id'];

// Cek apakah user adalah admin
$role_query = mysqli_query($conn, "SELECT role FROM users WHERE id='$user_id'");
$user_role = mysqli_fetch_assoc($role_query);
$is_admin = isset($user_role['role']) && $user_role['role'] === 'admin';

// Admin bisa hapus semua postingan, user biasa hanya postingan sendiri
if ($is_admin) {
    // Admin bisa hapus semua postingan
    mysqli_query($conn, "DELETE FROM comments WHERE status_id='$post_id'");
    mysqli_query($conn, "DELETE FROM statuses WHERE id='$post_id'");
    $redirect = "../admin/admin.php";
} else {
    // User biasa hanya bisa hapus postingan sendiri
    $query = mysqli_query($conn, "SELECT * FROM statuses WHERE id='$post_id' AND user_id='$user_id'");
    if (mysqli_num_rows($query) > 0) {
        mysqli_query($conn, "DELETE FROM comments WHERE status_id='$post_id'");
        mysqli_query($conn, "DELETE FROM statuses WHERE id='$post_id'");
    }
    $redirect = "../users/index.php";
}

mysqli_close($conn);
header("Location: " . $redirect);
exit;
?>
