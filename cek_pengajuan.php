<?php
session_start();
include 'config.php'; // Koneksi database

// Ambil logo dari tabel settings
$logo = "icon.png"; // default
$logoQuery = mysqli_query($conn,"SELECT logo FROM settings LIMIT 1");
if(mysqli_num_rows($logoQuery) > 0){
    $rowLogo = mysqli_fetch_assoc($logoQuery);
    if(!empty($rowLogo['logo'])){
        $logo = "uploads/".$rowLogo['logo']; // folder tempat logo diupload
    }
}

$msg = '';

// Proses pencarian pengajuan
$pengajuan = [];
if(isset($_POST['cek_pengajuan'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $query = mysqli_query($conn, "SELECT * FROM pengajuan WHERE nama LIKE '%$nama%' ORDER BY tgl_pengajuan DESC");
    while($row = mysqli_fetch_assoc($query)){
        $pengajuan[] = $row;
    }
}

// Daftar jenis surat untuk mapping file
$surat_file = [
    "Surat Usaha" => "surat/surat_usaha.php",
    "Surat Domisili" => "surat/domisili.php",
    "Surat Tidak Mampu" => "surat/surat_tidak_mampu.php",
    "Keterangan Kematian" => "surat/kematian.php"
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cek Pengajuan Surat</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{ font-family:'Poppins',sans-serif; background:#fff; margin:0; padding:0;}
header{ background:#50C878; color:white; padding:15px 40px; display:flex; justify-content: space-between; align-items:center;}
header .logo img{height:40px; margin-right:10px;}
header .logo span{font-weight:bold; font-size:1.5rem;}
nav a{ color:white; text-decoration:none; margin-left:20px; font-weight:600;}
nav a:hover{ opacity:0.8;}

.container{ max-width:950px; margin:50px auto; padding:30px; background:white; border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.1);}
h2{text-align:center; margin-bottom:25px; color:#2c3e50;}

form label{ display:block; margin-bottom:8px; font-weight:600;}
form input[type="text"]{ width:100%; padding:10px; margin-bottom:20px; border:1px solid #ccc; border-radius:5px;}
form button{ background:#50C878; color:white; padding:12px 25px; border:none; border-radius:5px; font-weight:600; cursor:pointer;}
form button:hover{ background:#46b06b;}

table{ width:100%; border-collapse:collapse; margin-top:20px;}
table th, table td{ border:1px solid #ddd; padding:10px; text-align:left; word-break:break-word; vertical-align:top;}
table th{ background:#50C878; color:white; }
.download-btn{ background:#50C878; color:white; padding:8px 12px; border-radius:5px; text-decoration:none; font-weight:600;}
.download-btn:hover{ background:#46b06b; }

.msg{ text-align:center; margin-top:20px; font-weight:600; color:green;}

.berkas-preview img, .berkas-preview iframe{ max-width:100%; max-height:200px; margin-bottom:10px; display:block;}
</style>
</head>
<body>

<header>
<div class="logo"><img src="<?= $logo ?>" alt="Logo"> <span>Sistem Surat Menyurat</span></div>
<nav>
<a href="index.php">Home</a>
<a href="layanan.php">Layanan</a>
<a href="pengajuan.php">Pengajuan</a>
<a href="cek_pengajuan.php">Cek Pengajuan</a>
<a href="login.php">Login</a>
</nav>
</header>

<div class="container">
<h2>Cek Pengajuan Surat</h2>

<form method="POST">
    <label for="nama">Masukkan Nama Lengkap</label>
    <input type="text" name="nama" id="nama" placeholder="Ketik nama Anda..." required>
    <button type="submit" name="cek_pengajuan">Cek Pengajuan</button>
</form>

<?php if(count($pengajuan) > 0){ ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Jenis Surat</th>
                <th>Status</th>
                <th>Tanggal Pengajuan</th>
                <th>Berkas</th>
                <th>Tanggapan</th>
                <th>Download Surat</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $no = 1;
        foreach($pengajuan as $row){ ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['nik']; ?></td>
                <td><?php echo $row['jenis_surat']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo date('d-m-Y', strtotime($row['tgl_pengajuan'])); ?></td>
                <td>
                    <div class="berkas-preview">
                    <?php 
                        $files = json_decode($row['berkas'], true);
                        if(is_array($files)){
                            foreach($files as $f){
                                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                                if($ext === "pdf"){
                                    echo '<iframe src="uploads/'.$f.'" width="100%" height="150px"></iframe>';
                                } else {
                                    echo '<img src="uploads/'.$f.'" alt="Berkas">';
                                }
                            }
                        }
                    ?>
                    </div>
                </td>
                <td>
                    <?php echo isset($row['tanggapan']) ? $row['tanggapan'] : '-'; ?>
                </td>
                <td>
                <?php 
                    if($row['status'] == 'Disetujui'){
                        $file = isset($surat_file[$row['jenis_surat']]) ? $surat_file[$row['jenis_surat']] : '';
                        if($file){
                            echo '<a href="'.$file.'?id='.$row['id'].'" target="_blank" class="download-btn">Download</a>';
                        } else {
                            echo 'File surat tidak tersedia';
                        }
                    } else {
                        echo 'Belum Bisa Diunduh';
                    }
                ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } elseif(isset($_POST['cek_pengajuan'])){ ?>
    <p class="msg">Data pengajuan tidak ditemukan.</p>
<?php } ?>

</div>

</body>
</html>
