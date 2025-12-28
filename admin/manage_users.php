<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Cek apakah user adalah admin
$conn = mysqli_connect("localhost", "root", "", "database");
$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT role FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
if (!isset($user['role']) || $user['role'] !== 'admin') {
    header("Location: ../users/index.php");
    exit;
}

// Handle delete user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $delete_user_id = (int)$_POST['delete_user'];
    
    if ($delete_user_id != $user_id) {
        mysqli_query($conn, "DELETE FROM users WHERE id=$delete_user_id");
        header("Location: ../admin/manage_users.php");
        exit;
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - SOVIA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .user-card {
            background: #111;
            border: 1px solid #222;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #333;
        }
        .user-details h3 {
            margin: 0;
            color: #fff;
        }
        .user-details p {
            margin: 5px 0 0 0;
            color: #999;
            font-size: 14px;
        }
        .admin-badge {
            background: #ff0;
            color: #000;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 5px;
        }
        .delete-user-btn {
            background: #f55;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
        }
        .delete-user-btn:hover {
            background: #d44;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #00ff6a;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header class="main-header" style="border-bottom :none;">
       
<div class="login-header-home" style="margin-top: 10px;
    margin-left: 25px;">
            <img src="../uploads/logo.png" alt="Logo" class="login-logo">
            <div>
                <h2 class="app-name" style="margin-top: 7px;">SOVIA <span class="admin-badge">ADMIN</span></h2>
                <p class="app-subtitle" style="margin-top: 5px;">Manage Users</p>
            </div>
            <div style="margin-left: 220px; margin-top: 10px;">
              <h2>User Management</h2>  
            </div>
            
        </div>
        <div style="margin-top : 25px;">
                 <a href="../admin/admin.php" title="Back to Admin"><img src="../uploads/search.png" class="" style="font-size: 100px; "></a>
            </div>

        
        
         <div>
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="feed">
        <a href="../admin/admin.php" class="back-btn">← Back to Admin Panel</a>
        
        <h2 style="color: #fff; margin-bottom: 20px;">All Users</h2>
        
        <?php
        $conn = mysqli_connect("localhost", "root", "", "database");
        // Tabel users tidak punya kolom created_at, jadi urutkan berdasarkan id saja
        $result = mysqli_query($conn, "SELECT id, username, avatar, role FROM users ORDER BY id DESC");
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='user-card'>";
            echo "<div class='user-info'>";
            $avatar = $row['avatar'] ? htmlspecialchars($row['avatar']) : 'default.png';
            echo "<img src='../uploads/$avatar' class='user-avatar'>";
            echo "<div class='user-details'>";
            echo "<h3>" . htmlspecialchars($row['username']);
            if (isset($row['role']) && $row['role'] === 'admin') {
                echo "<span class='admin-badge'>ADMIN</span>";
            }
            echo "</h3>";
            echo "<p>User ID: " . $row['id'] . "</p>";
            echo "</div>";
            echo "</div>";
            
            // Jangan tampilkan tombol delete untuk admin sendiri
            if ($row['id'] != $user_id) {
                echo "<form method='POST' onsubmit='return confirm(\"Are you sure you want to delete user " . htmlspecialchars($row['username']) . "? This will delete all their posts and comments.\")'>";
                echo "<input type='hidden' name='delete_user' value='" . $row['id'] . "'>";
                echo "<button type='submit' class='delete-user-btn'>Delete User</button>";
                echo "</form>";
            } else {
                echo "<span style='color: #999;'>Current User</span>";
            }
            
            echo "</div>";
        }
        mysqli_close($conn);
        ?>
    </div>
</body>
</html>

