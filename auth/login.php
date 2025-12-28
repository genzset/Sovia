<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | SOVIA</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="login-body">

<div class="login-wrapper">
    <!-- Bagian kiri -->
    <div class="login-left">
        <div class="login-header">
            <img src="../uploads/logo.png" alt="Logo" class="login-logo">
            <div>
                <h2 class="app-name" style="margin-top: 7px;">SOVIA</h2>
                <p class="app-subtitle" style="margin-top: 5px;">Social Sharing Platform</p>
            </div>
        </div>

        <div class="login-form-container">
            <h1 class="login-title">Masuk</h1>
            <p class="login-subtitle">Masukkan username dan password anda untuk masuk ke akun anda</p>

            <form action="../auth/login.php" method="post" onsubmit="return validateLogin()">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="misal: Timothy Ronald" required>

                <div class="label-flex">
                    <label for="password">Password</label>
                   
                </div>
                <input type="password" name="password" id="password" placeholder="Masukkan password" required>

                <button type="submit" class="btn-orange">Masuk</button>
            </form>

            <p class="register-link">
                Belum punya akun? <a href="../auth/register.php">Daftar</a>
            </p>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $conn = mysqli_connect("localhost", "root", "", "database");
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $password = $_POST['password'];
                $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
                $user = mysqli_fetch_assoc($result);

                if ($user && password_verify($password, $user['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = isset($user['role']) ? $user['role'] : 'user';
                    
                    // Redirect admin ke admin.php, user biasa ke index.php
                    if (isset($user['role']) && $user['role'] === 'admin') {
                        header("Location: ../admin/admin.php");
                    } else {
                        header("Location: ../users/index.php");
                    }
                    exit;
                } else {
                    echo "<div class='alert-error'>Username atau password salah!</div>";
                }

                mysqli_close($conn);
            }
            ?>
        </div>
    </div>

    <!-- Bagian kanan -->
    <div class="login-right">
        <img src="../uploads/library.png" alt="Library" class="bg-image">
        <div class="overlay"></div>
    </div>
</div>

<script src="../assets/script.js"></script>
</body>
</html>
