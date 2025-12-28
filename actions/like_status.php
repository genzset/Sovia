<?php
$conn = mysqli_connect("localhost", "root", "", "database");
$id = (int)$_GET['id'];
mysqli_query($conn, "UPDATE statuses SET likes = likes + 1 WHERE id=$id");
mysqli_close($conn);
header("Location: ../users/index.php");
?>