<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "database"; 

$con = mysqli_connect($host, $user, $pass, $db);
if (!$con) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
