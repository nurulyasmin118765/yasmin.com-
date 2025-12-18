<?php
include 'config.php';

// Ambil logo dari tabel settings
$logo = "icon.png"; // default
$logoQuery = mysqli_query($conn,"SELECT logo FROM settings LIMIT 1");
if(mysqli_num_rows($logoQuery) > 0){
    $rowLogo = mysqli_fetch_assoc($logoQuery);
    if(!empty($rowLogo['logo'])){
        $logo = "uploads/".$rowLogo['logo']; // folder tempat logo diupload
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Surat Menyurat</title>

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
        html, body { height:100%; }
        body { display:flex; flex-direction:column; min-height:100vh; background-color:#ffffff; }

        header {
            background-color:white; color:#50C878;
            display:flex; justify-content:space-between; align-items:center;
            padding:15px 40px; box-shadow:0 2px 5px rgba(0,0,0,0.1); z-index:10;
        }

        .logo { display:flex; align-items:center; font-weight:700; font-size:1.5rem; color:#50C878; }
        .logo img { height:42px; margin-right:12px; }

        nav a {
            color:#50C878; text-decoration:none; margin-left:25px; font-weight:600;
            transition:0.3s; padding:8px 12px; border-radius:5px;
        }
        nav a:hover { background-color:#50C878; color:white; }

        main { flex:1; }
        .hero { display:flex; justify-content:space-between; align-items:center; background-color:#ffffff; color:#2c3e50; padding:0 40px; min-height:calc(100vh - 80px); }
        .hero-text { flex:1; padding-right:20px; }
        .hero-text h1 { font-size:3rem; font-weight:700; color:#2c3e50; margin-bottom:20px; }
        .hero-text p { font-size:1.2rem; margin-bottom:30px; color:#555; }
        .btn-hero { background-color:#50C878; color:white; padding:12px 25px; font-weight:600; text-decoration:none; border-radius:5px; transition:0.3s; }
        .btn-hero:hover { background-color:#46b06b; }
        .hero-image { flex:1; text-align:center; }
        .hero-image img { max-width:80%; border-radius:10px; }

        footer { background-color:#50C878; color:white; text-align:center; padding:20px; margin-top:30px; }

        @media(max-width:992px){
            .hero { flex-direction:column-reverse; text-align:center; min-height:auto; padding:50px 20px; }
            .hero-text { padding-right:0; }
            .hero-image { margin-bottom:30px; }
            .hero-text h1 { font-size:2rem; }
            .hero-text p { font-size:1rem; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="logo">
            <img src="<?= $logo ?>" alt="Logo Surat">
            <span>Sistem Surat Menyurat</span>
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="layanan.php">Layanan</a>
            <a href="pengajuan.php">Pengajuan</a>
            <a href="cek_pengajuan.php">Cek Pengajuan</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <section class="hero">
            <div class="hero-text">
                <h1>Selamat Datang di Sistem Surat Menyurat</h1>
                <p>Kelola surat masuk dan surat keluar secara cepat, mudah, dan terstruktur dalam satu sistem digital.</p>
                <a href="login.php" class="btn-hero">Mulai Sekarang</a>
            </div>

            <div class="hero-image">
                <img src="surat.png" alt="Ilustrasi Surat">
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        &copy; <?= date('Y'); ?> Sistem Surat Menyurat. Semua Hak Dilindungi.
    </footer>

</body>
</html>
