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
mysqli_close($conn);

$logged_in = true;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - SOVIA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/script.js"></script>
    <style>
        .more-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }
        .more-menu {
            display: none;
            position: absolute;
            right: 20px;
            background: #111;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 10px;
            z-index: 10;
        }
        .more-menu button {
            display: block;
            width: 100%;
            background: none;
            border: none;
            color: #f55;
            padding: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        .more-menu button:hover {
            background: #222;
        }
        .cancel-btn {
            color: #ccc !important;
        }

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
        
        .admin-badge {
            background: #ff0;
            color: #000;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 5px;
        }

        
        .slide-sidebar {
            position: fixed;
            left: -400px;
            top: 0;
            width: 280px;
            height: 100vh;
            background: #111;
            border-right: 1px solid #333;
            z-index: 1000;
            transition: left 0.3s ease;
            padding: 20px;
            overflow-y: auto;
        }

        .slide-sidebar.open {
            left: 0;
        }

        .slide-sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .slide-sidebar-overlay.show {
            display: block;
        }

        .slide-sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #333;
        }

        .slide-sidebar-header h3 {
            margin: 0;
            color: #fff;
            font-size: 20px;
        }

        .close-sidebar-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide-sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .slide-sidebar-menu li {
            margin-bottom: 10px;
        }

        .slide-sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .slide-sidebar-menu a:hover {
            background: #222;
        }

        .slide-sidebar-menu a img {
            width: 20px;
            height: 20px;
        }

        .menu-icon {
            width: 20px;
            height: 20px;
            filter: brightness(0) invert(1);
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
                <p class="app-subtitle" style="margin-top: 5px;">Admin Panel</p>
            </div>
            <div style="margin-left: 80px; margin-top: 10px;">
              <h2>Admin Dashboard</h2>  
            </div>
            
        </div>
        <div style="margin-top : 27px; margin-right:-220px; display:none;">
                 <a href="../users/index.php" title="Back to Feed"><img src="../uploads/search.png" class="" style="font-size: 100px; "></a>
            </div>

        
        
         <div>
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="feed">

    <div class="sidebar">
        <a href="../admin/admin.php" title="Home"><img src="../uploads/home.png" class="sidebar-icon"></a>
        <a href="../admin/tribes_admin.php" title="Tribe"><img src="../uploads/tribe.png" class="sidebar-icon"></a>

            <a href="#" class="sidebar-icon" style="margin-left: -5px;">
            <?php
                $conn = mysqli_connect("localhost", "root", "", "database");
                $result = mysqli_query($conn, "SELECT avatar FROM users WHERE id='$user_id'");
                $user = mysqli_fetch_assoc($result);
                $avatar = $user && $user['avatar'] ? htmlspecialchars($user['avatar']) : 'default.png';
                mysqli_close($conn);
            ?>
                <img src="../uploads/<?= $avatar ?>" class="sidebar-avatar" title="Profile">
        </a>

        <a href="#" title="Menu" onclick="toggleSlideSidebar(); return false;"><img src="../uploads/menu.png" class="sidebar-icon"></a>
    </div>

    <!-- Sidebar Slide dari Kiri -->
    <div class="slide-sidebar-overlay" id="sidebar-overlay" onclick="toggleSlideSidebar()"></div>
    <div class="slide-sidebar" id="slide-sidebar">
        <div class="slide-sidebar-header">
            <h3>Menu</h3>
            <button class="close-sidebar-btn" onclick="toggleSlideSidebar()">←</button>
        </div>
        <ul class="slide-sidebar-menu">
            <li>
                <a href="../admin/view_feedback.php">
                    <span style="font-size: 20px;">💬</span>
                    <span>Masukkan</span>
                </a>
            </li>
            <li>
                <a href="../admin/manage_users.php">
                    <span style="font-size: 20px;">👥</span>
                    <span>Manage Users</span>
                </a>
            </li>
        </ul>
    </div>

        <?php
        $conn = mysqli_connect("localhost", "root", "", "database");
        if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

        $result = mysqli_query($conn, "
            SELECT s.id, s.user_id, s.content, s.image, s.likes, s.created_at, u.username, u.avatar, s.tribe_id, t.name as tribe_name
            FROM statuses s 
            JOIN users u ON s.user_id = u.id 
            LEFT JOIN tribes t ON s.tribe_id = t.id
            ORDER BY s.created_at DESC
        ");

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='post-card' style='width: 500px; margin: 20px auto; background-color: transparent; border: none;'>";
            echo "<div class='post-header'>";
            echo "<img src='../uploads/" . ($row['avatar'] ? htmlspecialchars($row['avatar']) : 'default.png') . "' class='post-avatar'>";
            echo "<div class='post-user-info'>";
            echo "<span class='post-username'>" . htmlspecialchars($row['username']);
            if ($row['tribe_name']) {
                echo " in <a href='../users/tribe_detail.php?id=" . $row['tribe_id'] . "' style='color: #ff0; text-decoration: none;'>" . htmlspecialchars($row['tribe_name']) . "</a>";
            }
            echo "</span>";
            echo "<span class='post-date'>" . date('M d, Y H:i', strtotime($row['created_at'])) . "</span>";
            echo "</div>";

            // Admin bisa hapus semua postingan (tampilkan tombol langsung + menu titik tiga)
            echo "<div style='position: relative;'>";
           
            echo "<button class='more-btn' onclick='toggleMenu(".$row['id'].")'>⋯</button>";
            echo "<div class='more-menu' id='menu-".$row['id']."'>
                    <form action='../actions/delete_post.php' method='POST'>
                        <input type='hidden' name='id' value='".$row['id']."'>
                        <button type='submit'>🗑 Delete</button>
                    </form>
                    <button class='cancel-btn' onclick='toggleMenu(".$row['id'].")'>Cancel</button>
                  </div>";
            echo "</div>";

            echo "</div>"; // header selesai

            echo "<div class='post-body'>";
            echo "<p class='post-text'>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
            if ($row['image']) {
                echo "<div class='post-image-wrapper'>
                        <img src='../uploads/" . htmlspecialchars($row['image']) . "' class='post-image'>
                      </div>";
            }
            echo "</div>";

            echo "<div class='post-actions'>";
            echo "<a href='../actions/like_status.php?id=" . $row['id'] . "' class='action-btn'>❤️ " . $row['likes'] . "</a>";
            echo "<span class='action-btn' onclick='toggleComment(" . $row['id'] . ")'>💬 Comment</span>";
            echo "</div>";

            echo "<div class='comment-section' id='comment-" . $row['id'] . "' style='display:none;'>";
            echo "<form action='../actions/post_comment.php' method='post'>
                    <input type='hidden' name='status_id' value='" . $row['id'] . "'>
                    <textarea name='content' placeholder='Add a comment...'></textarea>
                    <button type='submit'>Post</button>
                  </form>";
            
            $comments = mysqli_query($conn, "
                SELECT c.content, u.username 
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.status_id=" . $row['id']
            );
            while ($comment = mysqli_fetch_assoc($comments)) {
                echo "<div class='comment'><strong>" . htmlspecialchars($comment['username']) . "</strong> " . nl2br(htmlspecialchars($comment['content'])) . "</div>";
            }
            echo "</div>";
            echo "</div>"; // post card
        }
        mysqli_close($conn);
        ?>
    </div>

    <script>
        function toggleMenu(id) {
            const menu = document.getElementById('menu-' + id);
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }
        
        function toggleSlideSidebar() {
            const sidebar = document.getElementById('slide-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }
        
        function toggleComment(statusId) {
            let commentSection = document.getElementById("comment-" + statusId);
            commentSection.style.display = commentSection.style.display === "none" ? "block" : "none";
        }
    </script>
</body>
</html>
