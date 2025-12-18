<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}
include 'config.php';

// GANTI LOGO
if(isset($_POST['update_logo'])){
    $target_dir = "uploads/";
    $file_name = basename($_FILES["logo"]["name"]);
    $target_file = $target_dir . $file_name;

    if(move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)){
        mysqli_query($conn, "UPDATE settings SET logo='$file_name' WHERE id=1");
        $msg = "Logo berhasil diperbarui!";
    } else {
        $msg = "Gagal mengunggah logo!";
    }
}

// TAMBAH KATEGORI LAYANAN
if(isset($_POST['tambah_kategori'])){
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    mysqli_query($conn, "INSERT INTO kategori_layanan (nama_kategori, created_at) VALUES ('$kategori', NOW())");
    $msg = "Kategori layanan berhasil ditambahkan!";
}

// HAPUS KATEGORI
if(isset($_GET['hapus_kategori'])){
    $id = intval($_GET['hapus_kategori']);
    mysqli_query($conn, "DELETE FROM kategori_layanan WHERE id=$id");
    $msg = "Kategori layanan berhasil dihapus!";
}

// Ambil logo saat ini
$logo_result = mysqli_query($conn, "SELECT logo FROM settings WHERE id=1 LIMIT 1");
$logo_row = mysqli_fetch_assoc($logo_result);
$current_logo = $logo_row ? $logo_row['logo'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengaturan Sistem</title>

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
    max-width:1100px;
    margin:50px auto;
    display:flex;
    flex-direction:column;
    align-items:center;
    padding:0 20px 40px;
}
h1{color:#2c3e50;margin-bottom:20px;text-align:center;}
.message{color:green; text-align:center; font-weight:bold; margin-bottom:15px;}

.container{
    width:100%;
    max-width:800px;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
    margin-bottom:30px;
}
.container h2{
    margin-bottom:15px;
    color:#50C878;
}

form input, form select{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    border:1px solid #ccc;
    border-radius:5px;
}

.btn{
    padding:10px 20px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-size:14px;
}
.btn-save{background:#27ae60; color:white;}
.btn-del{background:#e74c3c; color:white;}

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
table th{background:#50C878; color:white;}
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
    <h1>Pengaturan Sistem</h1>
    
    <div class="container">
        <h2>Ganti Logo</h2>
        <?php if(isset($msg)) echo '<div class="message">'.$msg.'</div>'; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="logo" required>
            <?php if($current_logo) echo "<img src='uploads/$current_logo' alt='Logo' style='height:80px; margin-top:10px;'>"; ?>
            <button type="submit" name="update_logo" class="btn btn-save">Simpan Logo</button>
        </form>
    </div>

    <div class="container">
        <h2>Kelola Kategori Layanan</h2>
        <form method="POST">
            <input type="text" name="kategori" placeholder="Nama Kategori Layanan" required>
            <button type="submit" name="tambah_kategori" class="btn btn-save">Tambah Kategori</button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Nama Kategori</th>
                <th>Aksi</th>
            </tr>
            <?php
            $kategori = mysqli_query($conn, "SELECT * FROM kategori_layanan ORDER BY id DESC");
            while($row = mysqli_fetch_assoc($kategori)){
                echo "
                <tr>
                    <td>$row[id]</td>
                    <td>$row[nama_kategori]</td>
                    <td>
                        <a href='setting.php?hapus_kategori=$row[id]' onclick=\"return confirm('Yakin ingin menghapus kategori ini?')\">
                            <button class='btn btn-del'>Hapus</button>
                        </a>
                    </td>
                </tr>
                ";
            }
            ?>
        </table>
    </div>
</div>

<footer>
    &copy; <?=date('Y')?> Sistem Surat Desa Palewai. All rights reserved.
</footer>

</body>
</html>
