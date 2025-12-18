<?php
session_start();
include 'config.php';

// Ambil logo dari tabel settings
$logo = "icon.png"; // default
$result = mysqli_query($conn, "SELECT logo FROM settings LIMIT 1");
if($row = mysqli_fetch_assoc($result)){
    if(!empty($row['logo'])){
        $logo = "uploads/".$row['logo'];
    }
}

// Ambil semua layanan dari database
$layanan = [];
$query = mysqli_query($conn, "SELECT * FROM kategori_layanan ORDER BY created_at DESC");
while($row = mysqli_fetch_assoc($query)){
    $layanan[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Layanan Sistem Surat</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f4f7f8; min-height:100vh; display:flex; flex-direction:column;}
header{background:#50C878; color:white; display:flex; justify-content:space-between; align-items:center; padding:10px 40px;}
header .logo{display:flex; align-items:center;}
header .logo img{height:42px; margin-right:12px;}
header .logo span{font-weight:700; font-size:1.5rem;}
nav a{color:white; text-decoration:none; margin-left:20px; font-weight:600; padding:8px 12px; border-radius:5px; transition:0.3s;}
nav a:hover{background:white; color:#50C878;}
main{flex:1; padding:40px; max-width:1200px; margin:auto;}
h1{text-align:center; margin-bottom:40px; color:#2c3e50;}
.grid{display:flex; flex-wrap:wrap; gap:30px; justify-content:center;}
.card{background:white; padding:20px; width:300px; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); transition:0.3s; display:flex; flex-direction:column; justify-content:space-between;}
.card:hover{transform:translateY(-5px); box-shadow:0 10px 20px rgba(0,0,0,0.2);}
.card h3{margin-bottom:15px; color:#2c3e50;}
.card p{font-size:0.9rem; color:#555; margin-bottom:15px;}
.card a{margin-top:auto; text-decoration:none; background:#50C878; color:white; padding:10px; text-align:center; border-radius:5px; font-weight:600;}
.card a:hover{background:#46b06b;}
footer{background:#50C878; color:white; text-align:center; padding:15px 0; margin-top:auto;}
</style>
</head>
<body>

<header>
    <div class="logo"><img src="<?= $logo ?>" alt="Logo"><span>Sistem Surat Menyurat</span></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="layanan.php">Layanan</a>
        <a href="pengajuan.php">Pengajuan</a>
        <a href="cek_pengajuan.php">Cek Pengajuan</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<main>
    <h1>Layanan Kami</h1>
    <div class="grid">
        <?php if(count($layanan) > 0): ?>
            <?php foreach($layanan as $l): ?>
                <div class="card">
                    <h3><?= $l['nama_kategori'] ?></h3>
                    <p>Dibuat pada: <?= date('d-m-Y H:i', strtotime($l['created_at'])) ?></p>
                    <a href="pengajuan.php?jenis=<?= urlencode($l['nama_kategori']) ?>">Ajukan Sekarang</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Tidak ada layanan tersedia.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    &copy; <?= date('Y') ?> Sistem Surat Menyurat. Semua hak dilindungi.
</footer>

</body>
</html>
