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

// Ambil semua feedback
$feedback_query = "SELECT f.*, u.username 
                   FROM feedback f 
                   JOIN users u ON f.user_id = u.id 
                   ORDER BY f.created_at DESC";
$feedback_result = mysqli_query($conn, $feedback_query);

$logged_in = true;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Masukan dari User - SOVIA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body {
            background: #000;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
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

        .feedback-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 20px;
        }

        .feedback-card {
            background: #111;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .feedback-user {
            font-weight: bold;
            color: #ff0;
        }

        .feedback-date {
            color: #aaa;
            font-size: 12px;
        }

        .feedback-message {
            color: #fff;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .feedback-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-unread {
            background: #ff0;
            color: #000;
        }

        .status-read {
            background: #333;
            color: #aaa;
        }

        .mark-read-btn {
            background: #00ff6a;
            color: #000;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
        }

        .mark-read-btn:hover {
            background: #00cc55;
        }

        .feedback-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .more-btn {
            background: rgba(0, 0, 0, 0.7);
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .more-btn:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        .more-menu {
            display: none;
            position: absolute;
            top: 30px;
            right: 0;
            background: #111;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 10px;
            z-index: 20;
            min-width: 120px;
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
            text-align: left;
        }

        .more-menu button:hover {
            background: #222;
        }

        .cancel-btn {
            color: #ccc !important;
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

        h1 {
            margin-bottom: 30px;
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
    </style>
</head>
<body>
    <header class="main-header" style="border-bottom:none;">
        <div class="login-header-home" style="margin-top: 10px; margin-left: 25px;">
            <img src="../uploads/logo.png" alt="Logo" class="login-logo">
            <div>
                <h2 class="app-name" style="margin-top: 7px;">SOVIA <span class="admin-badge">ADMIN</span></h2>
                <p class="app-subtitle" style="margin-top: 5px;">Admin Panel</p>
            </div>
            <div style="margin-left: 80px; margin-top: 10px;">
                <h2>Masukan dari User</h2>  
            </div>
        </div>
        <div style="margin-top: 27px;">
            <a href="../admin/admin.php" class="logout-btn" style="margin-right: 15px;">Kembali ke Admin</a>
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="sidebar">
        <a href="../users/index.php" title="Home"><img src="../uploads/home.png" class="sidebar-icon"></a>
        <a href="../users/tribes.php" title="Tribe"><img src="../uploads/tribe.png" class="sidebar-icon"></a>
        <a href="#" class="sidebar-icon" style="margin-left: -5px;">
            <?php
                $result = mysqli_query($conn, "SELECT avatar FROM users WHERE id='$user_id'");
                $user = mysqli_fetch_assoc($result);
                $avatar = $user && $user['avatar'] ? htmlspecialchars($user['avatar']) : 'default.png';
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

    <div class="feedback-container">
        <h1>Masukan dari User</h1>
        
        <?php if (isset($_SESSION['delete_success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['delete_success']; unset($_SESSION['delete_success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['delete_error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['delete_error']; unset($_SESSION['delete_error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (mysqli_num_rows($feedback_result) > 0): ?>
            <?php while ($feedback = mysqli_fetch_assoc($feedback_result)): ?>
                <div class="feedback-card" style="position: relative;">
                    <div class="feedback-header">
                        <div>
                            <span class="feedback-user"><?php echo htmlspecialchars($feedback['username']); ?></span>
                            <span class="feedback-date">• <?php echo date('d M Y, H:i', strtotime($feedback['created_at'])); ?></span>
                        </div>
                        <div class="feedback-actions">
                            <span class="feedback-status status-<?php echo $feedback['status']; ?>">
                                <?php echo $feedback['status'] === 'unread' ? 'Belum Dibaca' : 'Sudah Dibaca'; ?>
                            </span>
                            <?php if ($feedback['status'] === 'unread'): ?>
                                <form action="../actions/mark_feedback_read.php" method="POST" style="display: inline-block;">
                                    <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                    <button type="submit" class="mark-read-btn">Tandai Dibaca</button>
                                </form>
                            <?php endif; ?>
                            <div style="position: relative;">
                                <button class="more-btn" onclick="toggleFeedbackMenu(<?php echo $feedback['id']; ?>)">⋯</button>
                                <div class="more-menu" id="feedback-menu-<?php echo $feedback['id']; ?>">
                                    <form action="../actions/delete_feedback.php" method="POST">
                                        <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                        <button type="submit">🗑 Delete</button>
                                    </form>
                                    <button class="cancel-btn" onclick="toggleFeedbackMenu(<?php echo $feedback['id']; ?>)">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="feedback-message">
                        <?php echo nl2br(htmlspecialchars($feedback['message'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="feedback-card">
                <p style="text-align: center; color: #aaa;">Belum ada masukan dari user.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleSlideSidebar() {
            const sidebar = document.getElementById('slide-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }
        
        function toggleFeedbackMenu(id) {
            const menu = document.getElementById('feedback-menu-' + id);
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>

