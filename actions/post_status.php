<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$conn = mysqli_connect("localhost", "root", "", "database");
$user_id = $_SESSION['user_id'];
$content = mysqli_real_escape_string($conn, $_POST['content']);
$tribe_id = isset($_POST['tribe_id']) ? (int)$_POST['tribe_id'] : null;

$image = null;
if ($_FILES['image']['name']) {
    $image = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image);
}
if (!empty($content)) {
    
    if ($tribe_id) {
        $check_member = mysqli_query($conn, "SELECT * FROM tribe_members WHERE tribe_id='$tribe_id' AND user_id='$user_id'");
        if (mysqli_num_rows($check_member) == 0) {
            mysqli_close($conn);
            header("Location: ../users/tribe_detail.php?id=$tribe_id");
            exit;
        }
        $query = "INSERT INTO statuses (user_id, tribe_id, content, image) VALUES ($user_id, $tribe_id, '$content', " . ($image ? "'$image'" : "NULL") . ")";
    } else {
        $query = "INSERT INTO statuses (user_id, content, image) VALUES ($user_id, '$content', " . ($image ? "'$image'" : "NULL") . ")";
    }
    mysqli_query($conn, $query);
}
mysqli_close($conn);

if ($tribe_id) {
    header("Location: ../users/tribe_detail.php?id=$tribe_id");
} else {
    header("Location: ../users/index.php");
}
?>