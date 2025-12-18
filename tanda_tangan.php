<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'kepala_desa'){
    header("Location: login.php");
    exit;
}
include 'config.php';

// Proses upload tanda tangan
$msg = '';
if(isset($_POST['upload_ttd'])){
    $file = $_FILES['ttd'];
    $filename = $file['name'];
    $filetmp = $file['tmp_name'];
    $fileext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed = ['png','jpg','jpeg'];

    if(in_array($fileext, $allowed)){
        $newname = time().'_'.$filename;
        $upload_dir = 'uploads/ttd/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        if(move_uploaded_file($filetmp, $upload_dir.$newname)){
            // Nonaktifkan tanda tangan lama
            mysqli_query($conn,"UPDATE tanda_tangan SET aktif=0");

            // Simpan tanda tangan baru
            mysqli_query($conn,"INSERT INTO tanda_tangan (file_ttd, aktif) VALUES ('$newname',1)");
            $msg = "Tanda tangan berhasil diupload dan diaktifkan.";
        } else {
            $msg = "Gagal mengupload file.";
        }
    } else {
        $msg = "Format file tidak diperbolehkan. Gunakan PNG, JPG, JPEG.";
    }
}

// Ambil semua tanda tangan
$ttd_query = mysqli_query($conn,"SELECT * FROM tanda_tangan ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tanda Tangan Kepala Desa</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
html, body{height:100%;}
body{background:#f4f7f8; display:flex; flex-direction:column; min-height:100vh;}
header{background:#50C878;color:white;display:flex;justify-content:space-between;align-items:center;padding:10px 40px;box-shadow:0 2px 5px rgba(0,0,0,0.1);flex-wrap: wrap;}
header .logo{display:flex; align-items:center;}
header .logo img{height:42px; margin-right:12px;}
header .logo span{font-weight:700; font-size:1.5rem;}
nav{display:flex; flex-wrap: wrap; align-items:center;}
nav a{color:white;text-decoration:none;margin-left:20px;font-weight:600;padding:8px 15px;border-radius:5px;display:flex;align-items:center;transition:0.3s;}
nav a i{margin-right:8px;}
nav a:hover{background:white;color:#50C878;}
nav a.logout{background:#e74c3c;}
nav a.logout:hover{background:#c0392b;color:white;}
.main{flex:1;max-width:1000px;margin:50px auto;display:flex;flex-direction:column;align-items:center;padding:0 20px 40px;}
h1{color:#2c3e50;margin-bottom:10px;text-align:center;}
h2.welcome{font-size:1rem; color:#555; margin-bottom:40px; text-align:center;}
.card-container{background:white;padding:20px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,0.1);width:100%;max-width:800px;}
.card-container h2{margin-bottom:20px;color:#50C878;}
form input[type="file"]{width:100%;padding:10px;margin-bottom:15px;}
form button{background:#50C878;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;font-weight:600;}
form button:hover{background:#46b06b;}
.msg{color:green;font-weight:600;margin-bottom:15px;text-align:center;}
table{width:100%;border-collapse:collapse;}
table th, table td{padding:10px;border:1px solid #ccc;text-align:center;}
table th{background:#50C878;color:white;}
td img{width:80px;height:auto;}
.active-label{background:#50C878;color:white;padding:3px 8px;border-radius:5px;}
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
        <a href="laporan.php"><i class="fas fa-file-alt"></i> Laporan </a>
        <a href="tanda_tangan.php"><i class="fas fa-signature"></i> Tanda Tangan </a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</header>

<div class="main">
    <h1>Kelola Tanda Tangan Kepala Desa</h1>
    <h2 class="welcome">Upload tanda tangan terbaru yang akan digunakan di surat resmi desa.</h2>

    <div class="card-container">
        <?php if($msg) echo '<div class="msg">'.$msg.'</div>'; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Upload Tanda Tangan (PNG/JPG/JPEG)</label>
            <input type="file" name="ttd" required>
            <button type="submit" name="upload_ttd">Upload & Aktifkan</button>
        </form>

        <h2>Daftar Tanda Tangan</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Preview</th>
                <th>Upload At</th>
                <th>Status</th>
            </tr>
            <?php $no=1; while($row=mysqli_fetch_assoc($ttd_query)){ ?>
            <tr>
                <td><?=$no++?></td>
                <td><img src="uploads/ttd/<?=$row['file_ttd']?>" alt="TTD"></td>
                <td><?=$row['uploaded_at']?></td>
                <td>
                    <?php if($row['aktif']==1){ ?>
                        <span class="active-label">Aktif</span>
                    <?php } else { echo "Nonaktif"; } ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

<footer>
    &copy; <?=date('Y')?> Sistem Surat Desa Palewai. All rights reserved.
</footer>

</body>
</html>
