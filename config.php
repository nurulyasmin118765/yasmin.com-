<?php
// config.php
// ==============================
// Koneksi Database MySQL
// ==============================

// Database configuration
$host = "localhost";       // Host database, biasanya localhost
$db_name = "menyurat";   // Nama database
$username = "root";        // Username database
$password = "";            // Password database, kosong jika tidak ada

// Create connection
$conn = mysqli_connect($host, $username, $password, $db_name);

// Check connection
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Optional: set charset
mysqli_set_charset($conn, "utf8mb4");
?>
