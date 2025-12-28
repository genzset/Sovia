<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require "../config/koneksi.php";

$user_id = $_SESSION['user_id'];
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($message)) {
    $_SESSION['feedback_error'] = "Masukan tidak boleh kosong!";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$message = mysqli_real_escape_string($con, $message);

$query = "INSERT INTO feedback (user_id, message) VALUES ('$user_id', '$message')";

if (mysqli_query($con, $query)) {
    $_SESSION['feedback_success'] = "Masukan berhasil dikirim! Terima kasih atas feedback Anda.";
} else {
    $_SESSION['feedback_error'] = "Gagal mengirim masukan. Silakan coba lagi.";
}

mysqli_close($con);
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>

