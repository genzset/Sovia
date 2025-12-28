<?php


$conn = mysqli_connect("localhost", "root", "", "database");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Buat tabel feedback jika belum ada
$create_table = "CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'unread',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $create_table)) {
    echo "Tabel 'feedback' berhasil dibuat!<br>";
    echo "<strong>Jangan lupa hapus file ../admin/create_feedback_table.php setelah selesai!</strong><br>";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

