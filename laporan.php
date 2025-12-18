<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'petugas'){
    header("Location: login.php");
    exit;
}
include 'config.php';

// Default periode
$filter_type = $_POST['filter_type'] ?? 'harian';
$start_date  = $_POST['start_date'] ?? date('Y-m-d');
$end_date    = $_POST['end_date'] ?? date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Pengajuan & Surat Keluar</title>
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

form{
    background:white;
    padding:15px;
    border-radius:8px;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);
    width:100%;
    max-width:700px;
    margin-bottom:20px;
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    align-items:center;
    justify-content:center;
}
form select, form input{padding:8px;border-radius:5px;border:1px solid #ccc;}
.btn{padding:6px 12px;border:none;border-radius:5px;background:#3498db;color:white;cursor:pointer;}
.btn:hover{background:#2980b9;}

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
        <a href="petugas_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="data_penduduk.php"><i class="fas fa-users"></i> Data Penduduk</a>
        <a href="surat_masuk.php"><i class="fas fa-inbox"></i> Surat Masuk</a>
        <a href="surat_keluar.php"><i class="fas fa-paper-plane"></i> Surat Keluar</a>
        <a href="pengajuan_surat.php"><i class="fas fa-paper-plane"></i> Pengajuan Surat</a>
        <a href="statistik.php"><i class="fas fa-chart-bar"></i> Statistik</a>
        <a href="laporan.php"><i class="fas fa-envelope"></i> Laporan </a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</header>

<div class="main">
    <h1>Laporan Pengajuan & Surat Keluar</h1>
    <h2 class="welcome">Pilih periode laporan dan cetak laporan sesuai kebutuhan.</h2>

    <!-- FORM FILTER -->
    <form method="POST">
        <select name="filter_type" onchange="this.form.submit()">
            <option value="harian" <?=$filter_type=='harian'?'selected':''?>>Harian</option>
            <option value="mingguan" <?=$filter_type=='mingguan'?'selected':''?>>Mingguan</option>
            <option value="bulanan" <?=$filter_type=='bulanan'?'selected':''?>>Bulanan</option>
        </select>
        <input type="date" name="start_date" value="<?=$start_date?>" onchange="this.form.submit()">
        <input type="date" name="end_date" value="<?=$end_date?>" onchange="this.form.submit()" style="display:<?=$filter_type=='harian'?'none':'inline-block'?>">
    </form>

    <!-- TOMBOL CETAK -->
    <form method="GET" action="laporan_pdf.php" target="_blank">
        <input type="hidden" name="filter" value="<?=$filter_type?>">
        <input type="hidden" name="start" value="<?=$start_date?>">
        <input type="hidden" name="end" value="<?=$end_date?>">
        <button type="submit" class="btn"><i class="fas fa-print"></i> Cetak Laporan</button>
    </form>
</div>

<footer>
    &copy; <?=date('Y')?> Sistem Surat Desa Palewai. All rights reserved.
</footer>

</body>
</html>
