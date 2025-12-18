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
<title>Histori Aktivitas Sistem</title>

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
    max-width:1200px;
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

.search-input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    border:1px solid #ccc;
    border-radius:5px;
}
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
    <h1>Histori Aktivitas Sistem</h1>

    <?php
    // TABEL kategori_layanan
    echo '<div class="container">';
    echo '<h2>Kategori Layanan</h2>';
    echo '<table>';
    echo '<tr><th>ID</th><th>Nama Kategori</th><th>Created At</th></tr>';
    $query = mysqli_query($conn, "SELECT * FROM kategori_layanan ORDER BY id DESC");
    while($row = mysqli_fetch_assoc($query)){
        echo "<tr>
                <td>$row[id]</td>
                <td>$row[nama_kategori]</td>
                <td>$row[created_at]</td>
              </tr>";
    }
    echo '</table></div>';

    // TABEL penduduk
    echo '<div class="container">';
    echo '<h2>Penduduk</h2>';
    echo '<table>';
    echo '<tr><th>ID</th><th>Nama</th><th>NIK</th><th>TTL</th><th>Agama</th><th>JK</th><th>Pekerjaan</th><th>Alamat</th></tr>';
    $query = mysqli_query($conn, "SELECT * FROM penduduk ORDER BY id DESC");
    while($row = mysqli_fetch_assoc($query)){
        echo "<tr>
                <td>$row[id]</td>
                <td>$row[nama]</td>
                <td>$row[nik]</td>
                <td>$row[ttl]</td>
                <td>$row[agama]</td>
                <td>$row[jk]</td>
                <td>$row[pekerjaan]</td>
                <td>$row[alamat]</td>
              </tr>";
    }
    echo '</table></div>';

    // TABEL pengajuan
    echo '<div class="container">';
    echo '<h2>Pengajuan Surat</h2>';
    echo '<table>';
    echo '<tr><th>ID</th><th>Nama</th><th>NIK</th><th>TTL</th><th>Agama</th><th>JK</th><th>Pekerjaan</th><th>Alamat</th><th>Jenis Surat</th><th>Berkas</th><th>Status</th><th>Tanggal Pengajuan</th></tr>';
    $query = mysqli_query($conn, "SELECT * FROM pengajuan ORDER BY id DESC");
    while($row = mysqli_fetch_assoc($query)){
        echo "<tr>
                <td>$row[id]</td>
                <td>$row[nama]</td>
                <td>$row[nik]</td>
                <td>$row[ttl]</td>
                <td>$row[agama]</td>
                <td>$row[jk]</td>
                <td>$row[pekerjaan]</td>
                <td>$row[alamat]</td>
                <td>$row[jenis_surat]</td>
                <td>$row[berkas]</td>
                <td>$row[status]</td>
                <td>$row[tgl_pengajuan]</td>
              </tr>";
    }
    echo '</table></div>';

    // TABEL settings
    echo '<div class="container">';
    echo '<h2>Settings</h2>';
    echo '<table>';
    echo '<tr><th>ID</th><th>Logo</th></tr>';
    $query = mysqli_query($conn, "SELECT * FROM settings");
    while($row = mysqli_fetch_assoc($query)){
        echo "<tr>
                <td>$row[id]</td>
                <td><img src='uploads/$row[logo]' alt='Logo' style='height:50px;'></td>
              </tr>";
    }
    echo '</table></div>';

    // TABEL surat_keluar
    echo '<div class="container">';
    echo '<h2>Surat Keluar</h2>';
    echo '<table>';
    echo '<tr><th>ID</th><th>ID Pengajuan</th><th>File Surat</th><th>Tanggal Upload</th></tr>';
    $query = mysqli_query($conn, "SELECT * FROM surat_keluar ORDER BY id DESC");
    while($row = mysqli_fetch_assoc($query)){
        echo "<tr>
                <td>$row[id]</td>
                <td>$row[id_pengajuan]</td>
                <td>$row[file_surat]</td>
                <td>$row[tgl_upload]</td>
              </tr>";
    }
    echo '</table></div>';

    // TABEL surat_masuk
    echo '<div class="container">';
    echo '<h2>Surat Masuk</h2>';
    echo '<table>';
    echo '<tr><th>ID</th><th>No Surat</th><th>Tanggal</th><th>Pengirim</th><th>Perihal</th><th>Isi</th><th>Berkas</th></tr>';
    $query = mysqli_query($conn, "SELECT * FROM surat_masuk ORDER BY id DESC");
    while($row = mysqli_fetch_assoc($query)){
        echo "<tr>
                <td>$row[id]</td>
                <td>$row[no_surat]</td>
                <td>$row[tanggal]</td>
                <td>$row[pengirim]</td>
                <td>$row[perihal]</td>
                <td>$row[isi]</td>
                <td>$row[berkas]</td>
              </tr>";
    }
    echo '</table></div>';

    // TABEL users
    echo '<div class="container">';
    echo '<h2>Pengguna Sistem</h2>';
    echo '<table>';
    echo '<tr><th>ID</th><th>Username</th><th>Role</th><th>Created At</th></tr>';
    $query = mysqli_query($conn, "SELECT id, username, role, created_at FROM users ORDER BY id DESC");
    while($row = mysqli_fetch_assoc($query)){
        echo "<tr>
                <td>$row[id]</td>
                <td>$row[username]</td>
                <td>$row[role]</td>
                <td>$row[created_at]</td>
              </tr>";
    }
    echo '</table></div>';
    ?>
</div>

<footer>
    &copy; <?=date('Y')?> Sistem Surat Desa Palewai. All rights reserved.
</footer>

</body>
</html>
