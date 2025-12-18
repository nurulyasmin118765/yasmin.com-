<?php
session_start();

// 1. Tentukan URL Dashboard default (jika peran tidak ditemukan)
$dashboard_url = "login.php"; // Default ke halaman login jika sesi tidak valid

// 2. Cek peran pengguna (Role) dari sesi
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        $dashboard_url = "admin_dashboard.php";
    } elseif ($_SESSION['role'] === 'petugas') {
        $dashboard_url = "petugas_dashboard.php";
    } elseif ($_SESSION['role'] === 'kepala_desa') { // KONDISI BARU DITAMBAHKAN
        $dashboard_url = "kepala_dashboard.php";
    }
    // Jika ada peran lain (misalnya 'warga'), Anda bisa menambahkannya di sini
}

// Logout jika tombol "Ya, Logout" ditekan
if(isset($_POST['logout'])){
    // Hancurkan semua data sesi
    session_destroy();
    // Arahkan ke halaman login
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logout</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body{font-family:'Poppins',sans-serif;background:#f4f7f8;margin:0;padding:0;}
.modal{display:flex;align-items:center;justify-content:center;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);}
.modal-content{background:white;padding:30px;border-radius:10px;text-align:center;max-width:400px;width:90%;box-shadow:0 5px 15px rgba(0,0,0,0.2);}
.modal-content h2{margin-bottom:20px;color:#50C878;}
.modal-content button{padding:10px 20px;border:none;border-radius:5px;margin:5px;font-weight:600;cursor:pointer;}
.modal-content button.logout{background:#e74c3c;color:white;}
.modal-content button.cancel{background:#3498db;color:white;}
</style>
</head>
<body>

<div class="modal">
    <div class="modal-content">
        <h2>Konfirmasi Logout</h2>
        <p>Apakah Anda yakin ingin keluar dari sistem?</p>
        <form method="POST">
            <button type="submit" name="logout" class="logout"><i class="fas fa-sign-out-alt"></i> Ya, Logout</button>
            <button type="button" class="cancel" onclick="window.location.href='<?php echo $dashboard_url; ?>'"><i class="fas fa-times"></i> Batal</button>
        </form>
    </div>
</div>

</body>
</html>