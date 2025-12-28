<?php


$conn = mysqli_connect("localhost", "root", "", "database");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Tambahkan kolom role jika belum ada
$check_column = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'role'");
if (mysqli_num_rows($check_column) == 0) {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user' AFTER password");
    echo "Kolom 'role' berhasil ditambahkan.<br>";
}

// Cek apakah admin sudah ada
$check_admin = mysqli_query($conn, "SELECT * FROM users WHERE username='admin'");
if (mysqli_num_rows($check_admin) > 0) {
    echo "User admin sudah ada!<br>";
} else {
    // Buat user admin dengan password: admin123
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, password, role, avatar) VALUES ('admin', '$password', 'admin', 'default.png')";
    
    if (mysqli_query($conn, $query)) {
        echo "User admin berhasil dibuat!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "<strong>Jangan lupa hapus file ../admin/create_admin.php setelah selesai!</strong><br>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>




