<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | SOVIA</title>
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
            <h1 class="login-title">Daftar</h1>
            <p class="login-subtitle">Buat akun baru untuk mulai berbagi di SOVIA</p>

            <form action="../auth/register.php" method="post" enctype="multipart/form-data" onsubmit="return validateRegister()">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="misal: Timothy Ronald" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Masukkan password" required>

                <label for="avatar">Foto Profil</label>
                <input type="file" name="avatar" id="avatar" accept="image/*">

                <button type="submit" class="btn-orange">Daftar</button>
            </form>

            <p class="register-link">
                Sudah punya akun? <a href="../auth/login.php">Masuk</a>
            </p>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $conn = mysqli_connect("localhost", "root", "", "database");

                if (!$conn) {
                    die("<div class='alert-error'>Gagal koneksi ke database!</div>");
                }

                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                // Cek apakah username sudah ada
                $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
                if (mysqli_num_rows($check) > 0) {
                    echo "<div class='alert-error'>Username sudah digunakan!</div>";
                } else {
                    // Upload avatar (kalau ada)
                    $avatar = "default.png";
                    if (!empty($_FILES['avatar']['name'])) {
                        $avatarName = time() . "_" . basename($_FILES['avatar']['name']);
                        $target = "../uploads/" . $avatarName;
                        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                            $avatar = $avatarName;
                        }
                    }

                    $insert = mysqli_query($conn, "INSERT INTO users (username, password, avatar) VALUES ('$username', '$password', '$avatar')");
                    if ($insert) {
                        echo "<div class='alert-success'>Pendaftaran berhasil! <a href='../auth/login.php'>Masuk sekarang</a></div>";
                    } else {
                        echo "<div class='alert-error'>Terjadi kesalahan, coba lagi!</div>";
                    }
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
