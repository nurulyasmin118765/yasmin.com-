<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'petugas'){
    header("Location: login.php");
    exit;
}
include 'config.php';

// Statistik jumlah total warga
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM penduduk"))['total'];

// Statistik berdasarkan jenis kelamin
$jk_query = mysqli_query($conn, "SELECT jk, COUNT(*) as jumlah FROM penduduk GROUP BY jk");

// Statistik berdasarkan agama
$agama_query = mysqli_query($conn, "SELECT agama, COUNT(*) as jumlah FROM penduduk GROUP BY agama");

// Statistik berdasarkan pekerjaan
$pekerjaan_query = mysqli_query($conn, "SELECT pekerjaan, COUNT(*) as jumlah FROM penduduk GROUP BY pekerjaan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Statistik Warga</title>

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
h1{color:#2c3e50;margin-bottom:20px;text-align:center;}
.container{
    width:100%;
    background:white;
    padding:20px;
    margin-bottom:30px;
    border-radius:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
}
h2{margin-bottom:15px;color:#50C878;}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}
table th, table td{
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
}
table th{background:#50C878;color:white;}
.card{
    background:white;
    border-radius:10px;
    padding:20px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
    width:100%;
    margin-bottom:20px;
    text-align:center;
}
.card h2{margin-bottom:10px;color:#50C878;font-size:1.2rem;}
.card p{font-size:1.8rem;color:#333;margin:0;}
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
    <h1>Statistik Warga</h1>

    <!-- Total Warga -->
    <div class="card">
        <h2>Total Warga</h2>
        <p><?= $total ?></p>
    </div>

    <!-- Statistik Jenis Kelamin -->
    <div class="container">
        <h2>Jumlah Warga Berdasarkan Jenis Kelamin</h2>
        <table>
            <tr><th>Jenis Kelamin</th><th>Jumlah</th></tr>
            <?php while($row = mysqli_fetch_assoc($jk_query)): ?>
                <tr>
                    <td><?= $row['jk'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Statistik Agama -->
    <div class="container">
        <h2>Jumlah Warga Berdasarkan Agama</h2>
        <table>
            <tr><th>Agama</th><th>Jumlah</th></tr>
            <?php while($row = mysqli_fetch_assoc($agama_query)): ?>
                <tr>
                    <td><?= $row['agama'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Statistik Pekerjaan -->
    <div class="container">
        <h2>Jumlah Warga Berdasarkan Pekerjaan</h2>
        <table>
            <tr><th>Pekerjaan</th><th>Jumlah</th></tr>
            <?php while($row = mysqli_fetch_assoc($pekerjaan_query)): ?>
                <tr>
                    <td><?= $row['pekerjaan'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

<footer>
    &copy; <?=date('Y')?> Sistem Surat Desa Palewai. All rights reserved.
</footer>

</body>
</html>
