<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
$user_id = $logged_in ? $_SESSION['user_id'] : 0;

// Redirect admin ke admin panel
if ($logged_in) {
    $conn = mysqli_connect("localhost", "root", "", "database");
    $result = mysqli_query($conn, "SELECT role FROM users WHERE id='$user_id'");
    $user_role = mysqli_fetch_assoc($result);
    if (isset($user_role['role']) && $user_role['role'] === 'admin') {
        mysqli_close($conn);
        header("Location: ../auth/admin.php");
        exit;
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SOVIA</title>
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

        /* Sidebar Slide dari Kiri */
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

        .slide-sidebar-menu a,
        .slide-sidebar-menu button {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            color: #fff;
            text-decoration: none;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            border-radius: 10px;
            transition: background 0.2s;
            cursor: pointer;
            font-size: 16px;
            font-family: inherit;
        }

        .slide-sidebar-menu a:hover,
        .slide-sidebar-menu button:hover {
            background: #222;
        }

        /* Form Masukan Modal */
        .feedback-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .feedback-modal.show {
            display: flex;
        }

        .feedback-modal-content {
            background: #111;
            border: 1px solid #333;
            border-radius: 15px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .feedback-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .feedback-modal-header h3 {
            margin: 0;
            color: #fff;
        }

        .close-feedback-btn {
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

        .feedback-form textarea {
            width: 100%;
            min-height: 150px;
            background: #000;
            color: #fff;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 15px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            box-sizing: border-box;
        }

        .feedback-form button[type="submit"] {
            margin-top: 15px;
            width: 100%;
            background: #00ff6a;
            border: none;
            color: #000;
            font-weight: bold;
            border-radius: 10px;
            padding: 12px 0;
            cursor: pointer;
            font-size: 16px;
        }

        .feedback-form button[type="submit"]:hover {
            background: #00cc55;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .alert-success {
            background: #00ff6a;
            color: #000;
        }

        .alert-error {
            background: #ff4444;
            color: #fff;
        }
        
        /* Override for post-form to center it */
        #post-form {
            position: fixed !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            background: #111 !important;
            border: 1px solid #222 !important;
            border-radius: 20px !important;
            padding: 30px !important;
            width: 90% !important;
            max-width: 600px !important;
            z-index: 1000 !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5) !important;
            box-sizing: border-box !important;
        }
        
        #post-form form {
            width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            box-sizing: border-box !important;
        }
        
        #post-form textarea {
            width: calc(100% - 30px) !important;
            min-height: 120px !important;
            max-height: 300px !important;
            background: #000 !important;
            color: #fff !important;
            border: 1px solid #333 !important;
            border-radius: 10px !important;
            padding: 15px !important;
            resize: vertical !important;
            font-family: inherit !important;
            font-size: 14px !important;
            box-sizing: border-box !important;
        }
        
        #post-form input[type="file"] {
            margin-top: 15px !important;
            color: #ccc !important;
            width: calc(100% - 30px) !important;
            padding: 10px !important;
            background: #000 !important;
            border: 1px solid #333 !important;
            border-radius: 10px !important;
            box-sizing: border-box !important;
        }
        
        #post-form button {
            margin-top: 15px !important;
            width: 100% !important;
            background: #00ff6a !important;
            border: none !important;
            color: #000 !important;
            font-weight: bold !important;
            border-radius: 10px !important;
            padding: 12px 0 !important;
            cursor: pointer !important;
            font-size: 16px !important;
            transition: background 0.2s !important;
        }
        
        #post-form button:hover {
            background: #00cc55 !important;
        }
    </style>
</head>
<body>
    <header class="main-header" style="border-bottom :none;">
       
<div class="login-header-home" style="margin-top: 10px;
    margin-left: 25px;">
            <img src="../uploads/logo.png" alt="Logo" class="login-logo">
            <div>
                <h2 class="app-name" style="margin-top: 7px;">SOVIA</h2>
                <p class="app-subtitle" style="margin-top: 5px;">Social Sharing Platform</p>
            </div>
            <div style="margin-left: 80px; margin-top: 10px;">
              <h2>Home</h2>  
            </div>
            
        </div>
        <div style="margin-top : 27px; margin-right:-250px; display:none;">
                 <a href="#" title="Search"><img src="../uploads/search.png" class="" style="font-size: 100px; "></a>
            </div>

        
       
         <div>
        <?php if ($logged_in): ?>
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        <?php else: ?>
            <a href="../auth/login.php" class="login-btn">Login</a>
        <?php endif; ?>
        </div>
    </header>

    <div class="feed">

    <div class="sidebar" >
        <a href="../users/index.php" title="Home"><img src="../uploads/home.png" class="sidebar-icon"></a>
            <a href="../users/tribes.php" title="Tribe"><img src="../uploads/tribe.png" class="sidebar-icon"></a>

            <a href="#" class="sidebar-icon" style="margin-left: -5px;">
            <?php if ($logged_in): ?>
                <?php
                    $conn = mysqli_connect("localhost", "root", "", "database");
                    $result = mysqli_query($conn, "SELECT avatar FROM users WHERE id='$user_id'");
                    $user = mysqli_fetch_assoc($result);
                    $avatar = $user && $user['avatar'] ? htmlspecialchars($user['avatar']) : 'default.png';
                    mysqli_close($conn);
                ?>
                <img src="../uploads/<?= $avatar ?>" class="sidebar-avatar" title="Profile">
            <?php else: ?>
                <img src="../uploads/default.png" class="sidebar-avatar" title="Guest">
            <?php endif; ?>
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
            <?php if ($logged_in): ?>
                <li>
                    <button onclick="openFeedbackModal(); toggleSlideSidebar();">
                        <span style="font-size: 20px;">💬</span>
                        <span>Masukkan</span>
                    </button>
                </li>
                <li>
                    <a href="../auth/logout.php">
                        <span style="font-size: 20px;">🚪</span>
                        <span>Logout</span>
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="../auth/login.php">
                        <span style="font-size: 20px;">🔐</span>
                        <span>Login</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Modal Form Masukan -->
    <?php if ($logged_in): ?>
        <div class="feedback-modal" id="feedback-modal">
            <div class="feedback-modal-content">
                <div class="feedback-modal-header">
                    <h3>Kirim Masukan</h3>
                    <button class="close-feedback-btn" onclick="closeFeedbackModal()">×</button>
                </div>
                <?php if (isset($_SESSION['feedback_success'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['feedback_success']; unset($_SESSION['feedback_success']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['feedback_error'])): ?>
                    <div class="alert alert-error">
                        <?php echo $_SESSION['feedback_error']; unset($_SESSION['feedback_error']); ?>
                    </div>
                <?php endif; ?>
                <form class="feedback-form" action="../actions/submit_feedback.php" method="POST">
                    <textarea name="message" placeholder="Tulis masukan atau saran Anda di sini..." required></textarea>
                    <button type="submit">Kirim Masukan</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

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
            echo "<div class='post-header' >";
            echo "<img src='../uploads/" . ($row['avatar'] ? htmlspecialchars($row['avatar']) : 'default.png') . "' class='post-avatar' style=' width: 35px;
    height: 35px;'>";
            echo "<div class='post-user-info'>";
            echo "<span class='post-username' style='font-size: 14px;'>" . htmlspecialchars($row['username']);
            if ($row['tribe_name']) {
                echo " in <a href='../users/tribe_detail.php?id=" . $row['tribe_id'] . "' style='color: #ff0; text-decoration: none;'>" . htmlspecialchars($row['tribe_name']) . "</a>";
            }
            echo "</span>";
            echo "<span class='post-date' style='font-size: 12px;'>" . date('M d, Y H:i', strtotime($row['created_at'])) . "</span>";
            echo "</div>";

            // Tombol titik tiga hanya muncul untuk postingan milik user login
            if ($logged_in && $user_id == $row['user_id']) {
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
            }

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

    <?php if ($logged_in): ?>
        <div class="fab" onclick="togglePostForm()">＋</div>
        <div id="post-form-overlay" class="post-form-overlay" style="display:none;" onclick="togglePostForm()"></div>
        <div class="post-form" id="post-form" style="display:none;">
            <form action="../actions/post_status.php" method="post" enctype="multipart/form-data" onsubmit="return validateStatus()">
                <textarea name="content" id="content" placeholder="What's happening?" required></textarea>
                <input type="file" name="image" accept="image/*">
                <button type="submit">Post</button>
            </form>
        </div>
    <?php endif; ?>

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

        function openFeedbackModal() {
            const modal = document.getElementById('feedback-modal');
            if (modal) {
                modal.classList.add('show');
            }
        }

        function closeFeedbackModal() {
            const modal = document.getElementById('feedback-modal');
            if (modal) {
                modal.classList.remove('show');
            }
        }

        // Tutup modal saat klik di luar
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('feedback-modal');
            if (modal && event.target === modal) {
                closeFeedbackModal();
            }
        });

        // Auto buka modal jika ada pesan success/error
        <?php if (isset($_SESSION['feedback_success']) || isset($_SESSION['feedback_error'])): ?>
            window.addEventListener('load', function() {
                openFeedbackModal();
            });
        <?php endif; ?>
    </script>
</body>
</html>
