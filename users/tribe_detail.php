<?php
session_start();
require "../config/koneksi.php";

$logged_in = isset($_SESSION['user_id']);
$user_id = $logged_in ? $_SESSION['user_id'] : 0;

// Cek apakah user adalah admin - jika admin, redirect ke tribe_detail_admin.php
if ($logged_in) {
    $check_admin_redirect = mysqli_query($con, "SELECT role FROM users WHERE id='$user_id'");
    $admin_result_redirect = mysqli_fetch_assoc($check_admin_redirect);
    if (isset($admin_result_redirect['role']) && $admin_result_redirect['role'] === 'admin') {
        $tribe_id_redirect = isset($_GET['id']) ? $_GET['id'] : '';
        if ($tribe_id_redirect) {
            header("Location: ../admin/tribe_detail_admin.php?id=" . $tribe_id_redirect);
            exit;
        }
    }
}

$tribe_id = $_GET['id'];
$tribe_query = mysqli_query($con, "SELECT t.*, u.username as creator_name FROM tribes t LEFT JOIN users u ON t.created_by = u.id WHERE t.id='$tribe_id'");
$tribe = mysqli_fetch_assoc($tribe_query);

if (!$tribe) {
    header("Location: ../users/tribes.php");
    exit;
}

$is_member = false;
$is_creator = false;
$is_admin = false;
if ($logged_in) {
    $check_member = mysqli_query($con, "SELECT * FROM tribe_members WHERE tribe_id='$tribe_id' AND user_id='$user_id'");
    $is_member = mysqli_num_rows($check_member) > 0;
    $is_creator = ($tribe['created_by'] == $user_id);
    
    // Cek apakah user adalah admin
    $check_admin = mysqli_query($con, "SELECT role FROM users WHERE id='$user_id'");
    $admin_result = mysqli_fetch_assoc($check_admin);
    $is_admin = isset($admin_result['role']) && $admin_result['role'] === 'admin';
}

$member_count_query = mysqli_query($con, "SELECT COUNT(*) as count FROM tribe_members WHERE tribe_id='$tribe_id'");
$member_count = mysqli_fetch_assoc($member_count_query)['count'];

$posts_query = mysqli_query($con, "
    SELECT s.id, s.user_id, s.content, s.image, s.likes, s.created_at, u.username, u.avatar 
    FROM statuses s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.tribe_id='$tribe_id'
    ORDER BY s.created_at DESC
");
$posts = [];
while ($post = mysqli_fetch_assoc($posts_query)) {
    $posts[] = $post;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($tribe['name']); ?> - SOVIA</title>
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

.admin-badge {
    background: #ff0;
    color: #000;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: bold;
    margin-left: 5px;
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

/* ===== TRIBE BANNER ===== */
.tribe-banner {
    width: 100%;
    max-width: 800px;
    margin: 80px auto 20px auto;
    background: linear-gradient(135deg, #ffeb3b 0%, #ff9800 100%);
    border-radius: 20px;
    padding: 40px;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.back-btn {
    position: absolute;
    top: 20px;
    left: 20px;
    background: #000;
    border: none;
    color: #fff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.tribe-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 5px solid #000;
    margin-bottom: 15px;
}

.tribe-banner h1 {
    margin: 0;
    color: #000;
    font-size: 28px;
}

.tribe-handle {
    color: #444;
    font-size: 14px;
    margin: 5px 0;
}

.tribe-stats {
    color: #000;
    font-size: 14px;
    font-weight: bold;
}

.join-header-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    background: #000;
    color: #fff;
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    font-weight: bold;
    cursor: pointer;
    width: 85px; /* ✅ biar stabil */
    text-align: center; /* ✅ teks rata tengah */
    display: inline-block;
}




.join-header-btn:hover {
    background: #333;
}

.btn-joined {
    background: #666;
    color: #fff;
    width: 85px;
}

.btn-joined:hover {
    background: #777;
}

/* ===== TABS ===== */
.tribe-tabs {
    max-width: 800px;
    margin: 0 auto 30px auto;
    display: flex;
    gap: 30px;
    padding: 0 20px;
    border-bottom: 1px solid #333;
}

.tab {
    background: none;
    border: none;
    color: #aaa;
    font-size: 16px;
    font-weight: bold;
    padding: 15px 0;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.3s;
}

.tab:hover {
    color: #fff;
}

.tab.active {
    color: #ff0;
    border-bottom-color: #ff0;
}

/* ===== CONTENT ===== */
.tribe-content {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.info-section {
    background: #111;
    border-radius: 15px;
    padding: 30px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #222;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    color: #aaa;
    font-size: 14px;
}

.info-value {
    color: #fff;
    font-weight: bold;
}

/* POST FORM */
.post-form {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #111;
    border: 1px solid #222;
    border-radius: 20px;
    padding: 30px;
    width: 90%;
    max-width: 600px;
    z-index: 1000;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    box-sizing: border-box;
}

.post-form-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 999;
}

.post-form form {
    width: 100%;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
}

.post-form textarea {
    width: calc(100% - 30px);
    min-height: 120px;
    max-height: 300px;
    background: #000;
    border: 1px solid #333;
    border-radius: 10px;
    padding: 15px;
    color: #fff;
    font-size: 14px;
    resize: vertical;
    font-family: 'Poppins', sans-serif;
    box-sizing: border-box;
}

.post-more{
    background-color: transparent;
}

.post-form input[type="file"] {
    width: calc(100% - 30px);
    background: #000;
    border: 1px solid #333;
    border-radius: 10px;
    padding: 10px;
    color: #fff;
    font-size: 14px;
    margin-top: 10px;
    box-sizing: border-box;
}

.post-form button {
    width: 100%;
    padding: 12px;
    background: #00FF6A;
    color: #000;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    margin-top: 15px;
    transition: background 0.2s;
}

.post-form button:hover {
    background: #00FF6A;
}

/* POST CARD */
.post-card {
    background: #111;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

.post-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.post-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

.post-user-info {
    flex: 1;
}

.post-username {
    display: block;
    font-weight: bold;
    font-size: 14px;
}

.post-date {
    color: #666;
    font-size: 12px;
}

.post-text {
    margin: 15px 0;
    line-height: 1.6;
    font-size: 14px;
}

.post-image-wrapper {
    margin: 15px 0;
    border-radius: 10px;
    overflow: hidden;
}

.post-image {
    width: 100%;
    display: block;
}

.post-actions {
    display: flex;
    gap: 20px;
    padding-top: 15px;
    border-top: 1px solid #222;
}

.action-btn {
    background: none;
    border: none;
    color: #fff;
    cursor: pointer;
    font-size: 14px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-state-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.empty-state-text {
    font-size: 18px;
    margin-bottom: 10px;
}

.empty-state-desc {
    font-size: 14px;
    color: #555;
}

/* More Menu */
.more-btn {
    background: #000;
    border: none;
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 50%;
}

.more-menu {
    display: none;
    position: absolute;
    background: #111;
    border: 1px solid #333;
    border-radius: 10px;
    padding: 10px;
    z-index: 10;
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

/* FAB Button */
.fab {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #00ff6a;
    color: #000;
    font-size: 30px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 255, 106, 0.4);
    transition: all 0.3s;
    z-index: 100;
}

.fab:hover {
    background: #00cc55;
    transform: scale(1.1);
}
</style>
</head>
<body>
<header class="main-header" style="border-bottom:none;">
    <div class="login-header-home" style="margin-top:10px;margin-left:25px;">
        <img src="../uploads/logo.png" alt="Logo" class="login-logo">
        <div>
            <h2 class="app-name" style="margin-top:7px;">SOVIA <?php if ($is_admin): ?><span class="admin-badge">ADMIN</span><?php endif; ?></h2>
            <p class="app-subtitle" style="margin-top:5px;"><?php echo $is_admin ? 'Admin Panel' : 'Social Sharing Platform'; ?></p>
        </div>
        <?php if ($is_admin): ?>
            <div style="margin-left: 80px; margin-top: 10px;">
                <h2>Tribe Detail</h2>  
            </div>
        <?php endif; ?>
    </div>
    <div style="margin-top:25px;">
        <?php if ($is_admin): ?>
            <a href="../admin/admin.php" class="logout-btn" style="margin-right: 15px;">Kembali ke Admin</a>
        <?php endif; ?>
        <?php if ($logged_in): ?>
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        <?php else: ?>
            <a href="../auth/login.php" class="login-btn">Login</a>
        <?php endif; ?>
    </div>
</header>

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="<?php echo $is_admin ? '../admin/admin.php' : '../users/index.php'; ?>" title="Home"><img src="../uploads/home.png" class="sidebar-icon"></a>
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
            <?php if (!$is_admin): ?>
                <li>
                    <button onclick="openFeedbackModal(); toggleSlideSidebar();">
                        <span style="font-size: 20px;">💬</span>
                        <span>Masukkan</span>
                    </button>
                </li>
            <?php else: ?>
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
            <?php endif; ?>
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
<?php if ($logged_in && !$is_admin): ?>
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

<!-- TRIBE BANNER -->
<div class="tribe-banner">
    <button class="back-btn" onclick="window.location.href='<?php echo $is_admin ? '../admin/admin.php' : '../users/tribes.php'; ?>'">←</button>
    
    <?php if ($is_creator || $is_admin): ?>
        <div style="position: absolute; top: 20px; right: 60px;">
            <button class="more-btn" onclick="toggleTribeDetailMenu()" style="background: #000; color: #fff; border: none; padding: 8px 12px; border-radius: 50%; cursor: pointer; font-size: 18px; margin-top: 18px;">⋯</button>
            <div class="more-menu" id="tribe-detail-menu" style="right: 0; left: auto;">
                <form action="../actions/delete_tribe.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $tribe_id; ?>">
                    <button type="submit">🗑 Delete</button>
                </form>
                <button class="cancel-btn" onclick="toggleTribeDetailMenu()">Cancel</button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!$is_admin): ?>
        <?php if ($logged_in): ?>
            <?php if ($is_member): ?>
                <form method="POST" action="../actions/unjoin_tribe.php" style="position: absolute; top: 20px; right: 90px; <?php echo $is_creator ? '120px' : '20px'; ?>;">
                    <input type="hidden" name="tribe_id" value="<?php echo $tribe_id; ?>">
                    <button class="join-header-btn btn-joined" type="submit">Joined</button>
                </form>
            <?php else: ?>
                <form method="POST" action="../actions/join_tribe.php" style="position: absolute; top: 20px; right: 90px;">
                    <input type="hidden" name="tribe_id" value="<?php echo $tribe_id; ?>">
                    <button class="join-header-btn" type="submit">Join</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <a href="../auth/login.php" class="join-header-btn" style="position: absolute; top: 20px; right: 20px; text-decoration: none; display: inline-block;">Join</a>
        <?php endif; ?>
    <?php endif; ?>
    
    <img src="../uploads/tribes/<?php echo htmlspecialchars($tribe['image']); ?>" alt="<?php echo htmlspecialchars($tribe['name']); ?>" class="tribe-avatar">
    <h1><?php echo htmlspecialchars($tribe['name']); ?></h1>
    <p class="tribe-handle">Created by <?php echo htmlspecialchars($tribe['creator_name'] ?? 'Unknown'); ?></p>
    <p class="tribe-stats"><?php echo $member_count; ?> Members</p>
</div>

<!-- TABS -->
<div class="tribe-tabs">
    <button class="tab active" onclick="switchTab(0)">Trend</button>
    <button class="tab" onclick="switchTab(1)">About</button>
</div>

<!-- CONTENT -->
<div class="tribe-content">
    <!-- TREND TAB -->
    <div class="tab-content active" id="tab-0">
        <?php if ($logged_in && $is_member): ?>
        <!-- Post Form Overlay -->
        <div id="post-form-overlay" class="post-form-overlay" style="display:none;" onclick="togglePostForm()"></div>
        <!-- Post Form for Members -->
        <div class="post-form" id="post-form" style="display:none;">
            <form action="../actions/post_status.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tribe_id" value="<?php echo $tribe_id; ?>">
                <textarea name="content" placeholder="Share something with the tribe..." required></textarea>
                <input type="file" name="image" accept="image/*">
                <button type="submit">Post</button>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Posts List -->
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $row): ?>
            <div class="post-card">

<div class="post-header">
    <div class="post-header-left" style="display:flex">
        <img src="../uploads/<?php echo htmlspecialchars($row['avatar'] ?: 'default.png'); ?>" class="post-avatar">
        <div class="post-user-info">
            <span class="post-username"><?php echo htmlspecialchars($row['username']); ?></span>
            <span class="post-date"><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></span>
        </div>
    </div>

    <?php if ($logged_in && $user_id == $row['user_id']): ?>
    <div class="post-more">
        <button class="more-btn" style="background-color: transparent;" onclick="toggleMenu(<?php echo $row['id']; ?>)">⋯</button>

        <div class="more-menu" id="menu-<?php echo $row['id']; ?>">
            <form action="../actions/delete_post.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button type="submit">🗑 Delete</button>
            </form>
            <button class="cancel-btn" onclick="toggleMenu(<?php echo $row['id']; ?>)">Cancel</button>
        </div>
    </div>
    <?php endif; ?>
</div>


              


                <div class="post-body">
                    <p class="post-text"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                    <?php if ($row['image']): ?>
                        <div class="post-image-wrapper">
                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" class="post-image">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="post-actions">
                    <a href="../actions/like_status.php?id=<?php echo $row['id']; ?>" class="action-btn">❤️ <?php echo $row['likes']; ?></a>
                    <span class="action-btn">💬 Comment</span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <div class="empty-state-text">No Posts Yet</div>
                <div class="empty-state-desc">Be the first to share something in this tribe!</div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- ABOUT TAB -->
    <div class="tab-content" id="tab-1">
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Name</span>
                <span class="info-value"><?php echo htmlspecialchars($tribe['name']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Members</span>
                <span class="info-value"><?php echo $member_count; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Created</span>
                <span class="info-value"><?php echo date('d M Y', strtotime($tribe['created_at'])); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Created by</span>
                <span class="info-value"><?php echo htmlspecialchars($tribe['creator_name'] ?? 'Unknown'); ?></span>
            </div>
        </div>
    </div>
</div>

<?php if ($logged_in && $is_member): ?>
    <!-- FAB Button -->
    <div class="fab" onclick="togglePostForm()">＋</div>
<?php endif; ?>

<script>
function switchTab(index) {
    // Update tab buttons
    document.querySelectorAll('.tab').forEach((tab, i) => {
        if (i === index) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    // Update content
    document.querySelectorAll('.tab-content').forEach((content, i) => {
        if (i === index) {
            content.classList.add('active');
        } else {
            content.classList.remove('active');
        }
    });
}

function togglePostForm() {
    const form = document.getElementById('post-form');
    const overlay = document.getElementById('post-form-overlay');
    
    if (!form || !overlay) return;
    
    const isVisible = form.style.display === 'block';
    
    form.style.display = isVisible ? 'none' : 'block';
    overlay.style.display = isVisible ? 'none' : 'block';
}

function toggleTribeDetailMenu() {
    const menu = document.getElementById('tribe-detail-menu');
    if (menu) {
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }
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

function toggleMenu(id) {
    const menu = document.getElementById('menu-' + id);
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}

</script>

</body>
</html>