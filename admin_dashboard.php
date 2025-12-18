<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}
include 'config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
html, body{height:100%;}
body{background:#f4f7f8; display:flex; flex-direction:column; min-height:100vh;}
header{
    background:#50C878;
    color:white;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:10px 40px;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);
    flex-wrap: wrap;
}
header .logo{display:flex; align-items:center;}
header .logo img{height:42px; margin-right:12px;}
header .logo span{font-weight:700; font-size:1.5rem;}
nav{display:flex; flex-wrap: wrap; align-items:center;}
nav a{
    color:white;
    text-decoration:none;
    margin-left:20px;
    font-weight:600;
    padding:8px 15px;
    border-radius:5px;
    display:flex;
    align-items:center;
    transition:0.3s;
}
nav a i{margin-right:8px;}
nav a:hover{background:white; color:#50C878;}
nav a.logout{background:#e74c3c;}
nav a.logout:hover{background:#c0392b; color:white;}

.main{
    flex:1;
    max-width:1000px;
    margin:50px auto;
    display:flex;
    flex-direction:column;
    align-items:center;
    padding:0 20px 40px;
}
h1{color:#2c3e50;margin-bottom:10px;text-align:center;}
h2.welcome{font-size:1rem; color:#555; margin-bottom:40px; text-align:center;}

.cards{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    justify-content:center;
}
.card{
    background:white;
    border-radius:10px;
    padding:20px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
    width:220px;
    height:140px;
    text-align:center;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    transition:0.3s;
}
.card:hover{
    transform: translateY(-5px);
    box-shadow:0 5px 15px rgba(0,0,0,0.15);
}
.card h2{margin-bottom:10px;color:#50C878;font-size:1.1rem;}
.card p{font-size:1.5rem; color:#333; margin:0;}

/* Footer */
footer{
    background:white;
    color:#50C878;
    text-align:center;
    padding:15px 20px;
    font-size:0.9rem;
    border-top:2px solid #50C878;
    margin-top:auto;
} 
footer a{color:#50C878; text-decoration:none;}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="icon.png" alt="Logo">
        <span>Surat Menyurat</span>
    </div>
    <nav>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="kelola_akses.php"><i class="fas fa-key"></i> Kelola Akses</a>
        <a href="histori.php"><i class="fas fa-history"></i> Histori</a>
        <a href="setting.php"><i class="fas fa-cog"></i> Pengaturan</a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</header>

<div class="main">
    <h1>Dashboard Admin</h1>
    <h2 class="welcome">Selamat datang di Panel Admin! Semoga hari ini penuh semangat dan produktif dalam melayani warga.</h2>

    <div class="cards">
        <div class="card">
            <h2><i class="fas fa-users"></i> Penduduk</h2>
            <?php
            $penduduk = mysqli_query($conn,"SELECT COUNT(*) as total FROM penduduk");
            $row = mysqli_fetch_assoc($penduduk);
            echo '<p>'.$row['total'].'</p>';
            ?>
        </div>

        <div class="card">
            <h2><i class="fas fa-inbox"></i> Surat Masuk</h2>
            <?php
            $masuk = mysqli_query($conn,"SELECT COUNT(*) as total FROM pengajuan WHERE status='Menunggu'");
            $row = mysqli_fetch_assoc($masuk);
            echo '<p>'.$row['total'].'</p>';
            ?>
        </div>

        <div class="card">
            <h2><i class="fas fa-paper-plane"></i> Surat Keluar</h2>
            <?php
            $keluar = mysqli_query($conn,"SELECT COUNT(*) as total FROM surat_keluar ");
            $row = mysqli_fetch_assoc($keluar);
            echo '<p>'.$row['total'].'</p>';
            ?>
        </div>
    </div>
</div>

<footer>
    &copy; <?=date('Y')?> Sistem Surat Desa Palewai. All rights reserved.
</footer>

</body>
</html>
