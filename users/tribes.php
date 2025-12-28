<?php
session_start();
require "../config/koneksi.php";

$logged_in = isset($_SESSION['user_id']);
$user_id = $logged_in ? $_SESSION['user_id'] : 0;

// Cek apakah user adalah admin - jika admin, redirect ke tribes_admin.php
if ($logged_in) {
    $check_admin_tribes = mysqli_query($con, "SELECT role FROM users WHERE id='$user_id'");
    $admin_result_tribes = mysqli_fetch_assoc($check_admin_tribes);
    if (isset($admin_result_tribes['role']) && $admin_result_tribes['role'] === 'admin') {
        header("Location: ../admin/tribes_admin.php");
        exit;
    }
}

// ambil semua tribes dari database
$tribes = mysqli_query($con, "SELECT * FROM tribes ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Tribes - SOVIA</title>
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

/* header tengah */
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

.btn-create:hover {
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

.btn-join {
    background: #ff0;
    color: #000;
    padding: 6px 15px;
    border-radius: 8px;
    border: none;
    font-weight: bold;
    cursor: pointer;
}

.btn-join:hover {
    background: #ffe600;
}

.btn-joined {
    background: #666;
    color: #fff;
    padding: 6px 15px;
    border-radius: 8px;
    border: none;
    font-weight: bold;
    cursor: pointer;
}

.btn-joined:hover {
    background: #777;
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

<!-- TRIBE CONTENT -->
<div class="container">
    <h2 style="margin-left: -490px; margin-top: -50px; ">My Tribes</h2>

    <div class="tribe-header" style="padding: 25px 200px; margin-top: 0px;">
        <a href="../admin/create_tribe.php" class="btn-create">
        <img src="../uploads/add_tambah.png" style="margin-right:-60px; width:30px; height:30px;"></a>
        <a href="../admin/create_tribe.php" class="btn-create"> Create a Tribe</a>
    </div>

    <div class="tribe-grid">
        <?php while($t = mysqli_fetch_assoc($tribes)) { 
            // Check if user has already joined this tribe
            $is_member = false;
            $is_creator = false;
            $is_admin = false;
            if ($logged_in) {
                $check_member = mysqli_query($con, "SELECT * FROM tribe_members WHERE tribe_id='{$t['id']}' AND user_id='$user_id'");
                $is_member = mysqli_num_rows($check_member) > 0;
                $is_creator = ($t['created_by'] == $user_id);
                
                // Cek apakah user adalah admin
                $check_admin = mysqli_query($con, "SELECT role FROM users WHERE id='$user_id'");
                $admin_result = mysqli_fetch_assoc($check_admin);
                $is_admin = isset($admin_result['role']) && $admin_result['role'] === 'admin';
            }
        ?>
        <div class="tribe-card" style="cursor: pointer; position: relative;" onclick="window.location.href='../users/tribe_detail.php?id=<?php echo $t['id']; ?>'">
            <?php if ($is_creator): ?>
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
            <?php endif; ?>
            
            <img src="../uploads/tribes/<?php echo htmlspecialchars($t['image']); ?>" alt="">
            <h3><?php echo htmlspecialchars($t['name']); ?></h3>
            
            <?php if ($logged_in): ?>
                <?php if ($is_member): ?>
                    <form method="POST" action="../actions/unjoin_tribe.php" onclick="event.stopPropagation();">
                        <input type="hidden" name="tribe_id" value="<?php echo $t['id']; ?>">
                        <button class="btn-joined" type="submit">Joined</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="../actions/join_tribe.php" onclick="event.stopPropagation();">
                        <input type="hidden" name="tribe_id" value="<?php echo $t['id']; ?>">
                        <button class="btn-join" type="submit">Join</button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <form method="POST" action="../actions/join_tribe.php" onclick="event.stopPropagation();">
                    <input type="hidden" name="tribe_id" value="<?php echo $t['id']; ?>">
                    <button class="btn-join" type="submit">Join</button>
                </form>
            <?php endif; ?>
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
</div>

</body>
</html>
