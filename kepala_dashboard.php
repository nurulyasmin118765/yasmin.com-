<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'kepala_desa'){
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
<title>Kepala Desa Dashboard</title>
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
        <a href="kepala_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="surat_keluar.php"><i class="fas fa-paper-plane"></i> Surat Keluar</a>
        <a href="pengajuan_surat.php"><i class="fas fa-envelope"></i> Pengajuan Surat</a>
        <a href="tanda_tangan.php"><i class="fas fa-signature"></i> Tanda Tangan </a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</header>

<div class="main">
    <h1>Dashboard Kepala Desa</h1>
    <h2 class="welcome">Selamat datang di Panel Kepala Desa! Semoga hari ini penuh semangat dan produktif dalam mengelola administrasi desa.</h2>

    <div class="cards">
        <div class="card">
            <h2><i class="fas fa-paper-plane"></i> Surat Keluar</h2>
            <?php
            $keluar = mysqli_query($conn,"SELECT COUNT(*) as total FROM surat_keluar");
            $row = mysqli_fetch_assoc($keluar);
            echo '<p>'.$row['total'].'</p>';
            ?>
        </div>

        <div class="card">
            <h2><i class="fas fa-envelope"></i> Pengajuan Surat</h2>
            <?php
            $pengajuan = mysqli_query($conn,"SELECT COUNT(*) as total FROM pengajuan");
            $row = mysqli_fetch_assoc($pengajuan);
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
