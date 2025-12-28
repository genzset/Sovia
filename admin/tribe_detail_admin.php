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

require "../config/koneksi.php";
$logged_in = true;

$tribe_id = $_GET['id'];
$tribe_query = mysqli_query($con, "SELECT t.*, u.username as creator_name FROM tribes t LEFT JOIN users u ON t.created_by = u.id WHERE t.id='$tribe_id'");
$tribe = mysqli_fetch_assoc($tribe_query);

if (!$tribe) {
    header("Location: ../admin/tribes_admin.php");
    exit;
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
<title><?php echo htmlspecialchars($tribe['name']); ?> - Admin SOVIA</title>
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
        <div style="margin-left: 340px; margin-top: 10px ;">
            <h2>Tribe Detail</h2>  
        </div>
    </div>
    <div style="margin-top: 27px;">
        <a href="../admin/admin.php" class="logout-btn" style="margin-right: 15px;">Kembali ke Admin</a>
        <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>
</header>

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="../admin/admin.php" title="Home"><img src="../uploads/home.png" class="sidebar-icon"></a>
    <a href="../admin/tribes_admin.php" title="Tribe"><img src="../uploads/tribe.png" class="sidebar-icon"></a>

    <a href="#" class="sidebar-icon" style="margin-left: -5px;">
        <?php
            $result = mysqli_query($con, "SELECT avatar FROM users WHERE id='$user_id'");
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
            <a href="view_feedback.php">
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

<!-- TRIBE BANNER -->
<div class="tribe-banner">
    <button class="back-btn" onclick="window.location.href='../admin/tribes_admin.php'">←</button>
    
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
        <!-- Posts List -->
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $row): ?>
            <div class="post-card">

            
            <div class="post-header">
    <div style="display:flex; align-items:center; gap:10px;">
        <img src="../uploads/<?php echo htmlspecialchars($row['avatar'] ?: 'default.png'); ?>" class="post-avatar">
        <div class="post-user-info">
            <span class="post-username">
                <?php echo htmlspecialchars($row['username']); ?>
            </span>
            <span class="post-date">
                <?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?>
            </span>
        </div>
    </div>

    <!-- ADMIN: bisa hapus semua post -->
    <div style="position: relative;">
        <button class="more-btn" onclick="toggleMenu(<?php echo $row['id']; ?>)">⋯</button>

        <div class="more-menu" id="menu-<?php echo $row['id']; ?>" style="right:0; top:35px;">
            <form action="../actions/delete_post.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button type="submit">🗑 Delete</button>
            </form>
            <button type="button" class="cancel-btn" onclick="toggleMenu(<?php echo $row['id']; ?>)">Cancel</button>
        </div>
    </div>
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
                <div class="empty-state-desc">No posts in this tribe yet.</div>
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

<script>
function switchTab(index) {
    document.querySelectorAll('.tab').forEach((tab, i) => {
        if (i === index) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    document.querySelectorAll('.tab-content').forEach((content, i) => {
        if (i === index) {
            content.classList.add('active');
        } else {
            content.classList.remove('active');
        }
    });
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

function toggleMenu(id) {
    const menu = document.getElementById('menu-' + id);

    // tutup semua menu lain
    document.querySelectorAll('.more-menu').forEach(m => {
        if (m !== menu) m.style.display = 'none';
    });

    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}
</script>

</body>
</html>

