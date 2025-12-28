<?php
session_start();
require "../config/koneksi.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$logged_in = isset($_SESSION['user_id']);
$user_id = $logged_in ? $_SESSION['user_id'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tribe_name = mysqli_real_escape_string($con, $_POST['tribe_name']);
    $image = null;
    
    // Handle image upload
    if ($_FILES['tribe_image']['name']) {
        $image_name = $_FILES['tribe_image']['name'];
        $image_tmp = $_FILES['tribe_image']['tmp_name'];
        
        // Create tribes folder if it doesn't exist
        if (!file_exists('../uploads/tribes')) {
            mkdir('../uploads/tribes', 0777, true);
        }
        
        // Upload image to tribes folder
        move_uploaded_file($image_tmp, "../uploads/tribes/" . $image_name);
        $image = $image_name;
    }
    
    if (!empty($tribe_name)) {
        $query = "INSERT INTO tribes (name, image, created_by) VALUES ('$tribe_name', " . ($image ? "'$image'" : "'default_tribe.png'") . ", $user_id)";
        mysqli_query($con, $query);
        header("Location: ../users/tribes.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Tribe - SOVIA</title>
<link rel="stylesheet" href="../assets/style.css">
<style>
body {
    background: #000;
    color: #fff;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
}

/* ===== SIDEBAR ===== */
.sidebar {
    position: fixed;
    left: 80px;
    top: 160px;
    display: flex;
    flex-direction: column;
    gap: 75px;
    
}

.sidebar-icon {
    width: 25px;
    height: 25px;
    margin-bottom: 25px;
    cursor: pointer;
}

.sidebar-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid #222;
    cursor: pointer;
}

/* ===== HEADER ===== */
.main-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 40px;
    border-bottom: 1px solid #111;
    background: #000;
}

.login-header-home {
    display: flex;
    align-items: center;
    gap: 15px;
}

.login-logo {
    width: 50px;
    height: 50px;
}

.app-name {
    font-size: 24px;
    margin: 0;
}

.app-subtitle {
    font-size: 12px;
    color: #aaa;
    margin: 0;
}

.logout-btn {
    color: #0f0;
    text-decoration: none;
    font-weight: bold;
}

/* ===== FORM CONTAINER ===== */
.container {
    width: 60%;
    max-width: 600px;
    margin: 120px auto;
    background: #111;
    border-radius: 15px;
    padding: 40px;
}

.form-header {
    text-align: center;
    margin-bottom: 30px;
}

.form-header h1 {
    font-size: 28px;
    margin-bottom: 10px;
}

.form-header p {
    color: #aaa;
    font-size: 14px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #fff;
}

.form-group input[type="text"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #333;
    border-radius: 8px;
    background: #000;
    color: #fff;
    font-size: 14px;
    box-sizing: border-box;
}

.form-group input[type="file"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #333;
    border-radius: 8px;
    background: #000;
    color: #fff;
    font-size: 14px;
    box-sizing: border-box;
}

.form-group input[type="text"]:focus {
    outline: none;
    border-color: #ff0;
}

.form-group input[type="file"]:focus {
    outline: none;
    border-color: #ff0;
}

.btn-submit {
    width: 100%;
    padding: 12px;
    background: #ff0;
    color: #000;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    transition: 0.2s ease;
}

.btn-submit:hover {
    background: #ffe600;
}

.btn-cancel {
    display: inline-block;
    width: 100%;
    padding: 12px;
    background: transparent;
    color: #fff;
    border: 1px solid #333;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    transition: 0.2s ease;
    text-align: center;
    text-decoration: none;
    margin-top: 10px;
    box-sizing: border-box;
}

.btn-cancel:hover {
    background: #1a1a1a;
    border-color: #555;
}

.preview-image {
    margin-top: 15px;
}

.preview-image img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
    border: 1px solid #333;
}
</style>
</head>
<body>
<header class="main-header" style="border-bottom:none;">
    <div class="login-header-home" style="margin-top:10px;margin-left:25px;">
        <img src="../uploads/logo.png" alt="Logo" class="login-logo">
        <div>
            <h2 class="app-name" style="margin-top:7px;">SOVIA</h2>
            <p class="app-subtitle" style="margin-top:5px;">Social Sharing Platform</p>
        </div>
    </div>
    <div style="margin-top:25px;">
        <?php if ($logged_in): ?>
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        <?php else: ?>
            <a href="../auth/login.php" class="login-btn">Login</a>
        <?php endif; ?>
    </div>
</header>

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="../users/index.php" title="Home"><img src="../uploads/home.png" class="sidebar-icon"></a>
    <a href="../users/tribes.php" title="Tribe"><img src="../uploads/tribe.png" class="sidebar-icon"></a>

    <a href="#" class="sidebar-icon" style="margin-left:-5px;">
        <?php if ($logged_in): ?>
            <?php
                $result = mysqli_query($con, "SELECT avatar FROM users WHERE id='$user_id'");
                $user = mysqli_fetch_assoc($result);
                $avatar = $user && $user['avatar'] ? htmlspecialchars($user['avatar']) : 'default.png';
            ?>
            <img src="../uploads/<?= $avatar ?>" class="sidebar-avatar" title="Profile">
        <?php else: ?>
            <img src="../uploads/default.png" class="sidebar-avatar" title="Guest">
        <?php endif; ?>
    </a>

    <a href="#" title="Menu"><img src="../uploads/menu.png" class="sidebar-icon"></a>
</div>

<!-- FORM CONTAINER -->
<div class="container" style="margin-top: 10px;">
    <div class="form-header">
        <h1>Create a Tribe</h1>
        <p>Start your own community and connect with like-minded people</p>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="tribe_name">Tribe Name</label>
            <input type="text" id="tribe_name" name="tribe_name" placeholder="Enter tribe name" required>
        </div>

        <div class="form-group">
            <label for="tribe_image">Tribe Photo</label>
            <input type="file" id="tribe_image" name="tribe_image" accept="image/*">
            <div class="preview-image" id="preview" style="display:none;">
                <img id="previewImg" src="" alt="Preview">
            </div>
        </div>

        <button type="submit" class="btn-submit">Create Tribe</button>
        <a href="../users/tribes.php" class="btn-cancel">Cancel</a>
    </form>
</div>

<script>
// Preview image before upload
document.getElementById('tribe_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('preview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>
