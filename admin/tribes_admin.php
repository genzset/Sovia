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

// ambil semua tribes dari database
$tribes = mysqli_query($con, "SELECT * FROM tribes ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Tribes - Admin SOVIA</title>
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

/* ===== HEADER ===== */
.main-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 40px;
    border-bottom: 1px solid #111;
    background-color: transparent;
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

/* ===== TRIBE CONTENT ===== */
.container {
    width: 70%;
    margin: 100px auto;
    text-align: center;
    margin-top: 50px;
}

.tribe-header {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 25px;
    background: linear-gradient(90deg, #192113, #2a2a10);
    border-radius: 15px;
    padding: 15px 25px;
    width: fit-content;
    margin: 120px auto 50px auto;
}

.tribe-header h1 {
    font-size: 24px;
    font-weight: bold;
    margin: 0;
}

.btn-create {
    color: #fff;
    padding: 1px 18px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.2s ease;
    font-size: 20px;
}

.tribe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill,minmax(150px,1fr));
    gap: 20px;
    margin-left: 10px;
    margin-right: 10px;
}

.tribe-card {
    background: #111;
    border-radius: 15px;
    padding: 15px;
    transition: 0.3s;
}

.tribe-card:hover {
    background: #1a1a1a;
}

.tribe-card img {
    width: 100%;
    height: 100px;
    border-radius: 10px;
    object-fit: cover;
    margin-top: 10px;
}

.tribe-card h3 {
    margin: 10px 0 5px;
}

/* More Menu for Tribe */
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
             
        </div>
    </div>
    <div style="margin-top: 27px;">
        <a href="../admin/admin.php" class="logout-btn" style="margin-right: 15px;">Kembali ke Home</a>
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

<!-- TRIBE CONTENT -->
<div class="container">
    <h2 style="margin: 50px auto; ">All Tribes</h2>

    <div class="tribe-grid">
        <?php while($t = mysqli_fetch_assoc($tribes)) { ?>
        <div class="tribe-card" style="cursor: pointer; position: relative;" onclick="window.location.href='../admin/tribe_detail_admin.php?id=<?php echo $t['id']; ?>'">
            <div style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                <button class="more-btn" onclick="event.stopPropagation(); toggleTribeMenu(<?php echo $t['id']; ?>)">⋯</button>
                <div class="more-menu" id="tribe-menu-<?php echo $t['id']; ?>">
                    <form action="../actions/delete_tribe.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                        <button type="submit">🗑 Delete</button>
                    </form>
                    <button class="cancel-btn" onclick="event.stopPropagation(); toggleTribeMenu(<?php echo $t['id']; ?>)">Cancel</button>
                </div>
            </div>
            
            <img src="../uploads/tribes/<?php echo htmlspecialchars($t['image']); ?>" alt="">
            <h3><?php echo htmlspecialchars($t['name']); ?></h3>
        </div>
        <?php } ?>
    </div>

<script>
function toggleTribeMenu(id) {
    const menu = document.getElementById('tribe-menu-' + id);
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}

function toggleSlideSidebar() {
    const sidebar = document.getElementById('slide-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('show');
}
</script>
</div>

</body>
</html>

