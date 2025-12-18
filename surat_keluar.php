<?php
session_start();
include 'config.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];

// Mapping jenis surat → file template PHP
$surat_file = [
    "Surat Usaha" => "surat/surat_usaha.php",
    "Surat Domisili" => "surat/domisili.php",
    "Surat Tidak Mampu" => "surat/surat_tidak_mampu.php",
    "Keterangan Kematian" => "surat/kematian.php", 
    "Surat Lainnya" => "surat/layanan_lainnya.php"
];

// Tambah surat keluar
if(isset($_POST['add'])){
    $id_pengajuan = intval($_POST['id_pengajuan']);
    $file_surat = $_FILES['berkas'] ?? null;

    if($file_surat && $file_surat['name'] != ''){
        $filename = time().'_'.$file_surat['name'];
        move_uploaded_file($file_surat['tmp_name'], 'uploads/'.$filename);
    } else {
        $filename = NULL;
    }

    $tgl_upload = date('Y-m-d H:i:s');
    mysqli_query($conn,"INSERT INTO surat_keluar (id_pengajuan, file_surat, tgl_upload) VALUES ('$id_pengajuan','$filename','$tgl_upload')");
    header("Location: surat_keluar.php"); exit;
}

// Edit surat keluar
if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $file_surat = $_FILES['berkas'] ?? null;

    if($file_surat && $file_surat['name'] != ''){
        $filename = time().'_'.$file_surat['name'];
        move_uploaded_file($file_surat['tmp_name'], 'uploads/'.$filename);
        $file_sql = ", file_surat='$filename'";
    } else { $file_sql = ""; }

    mysqli_query($conn,"UPDATE surat_keluar SET id_pengajuan=".intval($_POST['id_pengajuan'])." $file_sql WHERE id=$id");
    header("Location: surat_keluar.php"); exit;
}

// Hapus surat keluar
if(isset($_POST['delete'])){
    $id = intval($_POST['id']);
    $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT file_surat FROM surat_keluar WHERE id=$id"));
    if($row['file_surat']) @unlink('uploads/'.$row['file_surat']);
    mysqli_query($conn,"DELETE FROM surat_keluar WHERE id=$id");
    header("Location: surat_keluar.php"); exit;
}

// Ambil data surat keluar
$surat = mysqli_query($conn,"SELECT sk.*, p.nama, p.nik, p.jenis_surat FROM surat_keluar sk JOIN pengajuan p ON sk.id_pengajuan = p.id ORDER BY sk.id DESC");

// AJAX untuk edit
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM surat_keluar WHERE id=$id"));
    echo json_encode($row);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Surat Keluar</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f4f7f8;}
header{background:#50C878;color:white;display:flex;justify-content:space-between;align-items:center;padding:10px 40px;box-shadow:0 2px 5px rgba(0,0,0,0.1);}
header .logo{display:flex;align-items:center;}
header .logo img{height:42px;margin-right:12px;}
header .logo span{font-weight:700;font-size:1.5rem;}
nav{display:flex;align-items:center;}
nav a{color:white;text-decoration:none;margin-left:20px;font-weight:600;padding:8px 15px;border-radius:5px;display:flex;align-items:center;transition:0.3s;}
nav a i{margin-right:8px;}
nav a:hover{background:white;color:#50C878;}
nav a.logout{background:#e74c3c;}
nav a.logout:hover{background:#c0392b;color:white;}
.container{max-width:1200px;margin:50px auto;padding:20px;background:white;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,0.1);}
h1{text-align:center;margin-bottom:20px;color:#2c3e50;}
button.add{background:#50C878;color:white;padding:10px 15px;margin-bottom:10px;border:none;border-radius:5px;cursor:pointer;}
button.add:hover{opacity:0.9;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
table, th, td{border:1px solid #ddd;}
th, td{padding:10px;text-align:center;}
th{background:#50C878;color:white;}
button.action{padding:6px 10px;border:none;border-radius:5px;cursor:pointer;}
button.edit{background:#3498db;color:white;margin-right:5px;}
button.delete{background:#e74c3c;color:white;}
a.view{background:#1c7c54;color:white;padding:5px 10px;border-radius:5px;text-decoration:none;}
a.view:hover{background:#145c3f;}

/* Modal */
.modal{display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);}
.modal-content{background:white;margin:50px auto;padding:20px;width:90%;max-width:500px;border-radius:10px;position:relative;}
.modal-content h2{margin-bottom:20px;}
.modal-content input, .modal-content select{width:100%;padding:10px;margin-bottom:15px;border:1px solid #ccc;border-radius:5px;}
.modal-content button.close{position:absolute;top:10px;right:10px;background:#e74c3c;color:white;padding:5px 10px;border:none;border-radius:5px;cursor:pointer;}
.modal-content button.save{background:#50C878;color:white;padding:10px 15px;border:none;border-radius:5px;cursor:pointer;margin-right:10px;}
.modal-content button.del{background:#e74c3c;color:white;padding:10px 15px;border:none;border-radius:5px;}
footer{background:white;color:#50C878;text-align:center;padding:15px 20px;font-size:0.9rem;border-top:2px solid #50C878;margin-top:auto;}
footer a{color:#50C878;text-decoration:none;}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function openForm(id=''){
    if(id != ''){
        $.get('surat_keluar.php?id='+id,function(data){
            let row = JSON.parse(data);
            $('#modal input[name="id"]').val(row.id);
            $('#modal select[name="id_pengajuan"]').val(row.id_pengajuan);
            $('#modal button[name="add"]').hide();
            $('#modal button[name="edit"]').show();
            $('#modal').show();
        });
    } else {
        $('#modal form')[0].reset();
        $('#modal input[name="id"]').val('');
        $('#modal button[name="edit"]').hide();
        $('#modal button[name="add"]').show();
        $('#modal').show();
    }
}

function openDelete(id){
    $('#deleteModal input[name="id"]').val(id);
    $('#deleteModal').show();
}

function closeModal(){
    $('.modal').hide();
}
</script>
</head>
<body>

<header>
    <div class="logo"><img src="icon.png" alt="Logo"><span>Surat Menyurat</span></div>

    <nav>
        <?php if($role == 'admin'){ ?>
            <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="kelola_akses.php"><i class="fas fa-key"></i> Kelola Akses</a>
            <a href="histori.php"><i class="fas fa-history"></i> Histori</a>
            <a href="setting.php"><i class="fas fa-cog"></i> Pengaturan</a>
        <?php } elseif($role == 'petugas'){ ?>
            <a href="petugas_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="data_penduduk.php"><i class="fas fa-users"></i> Data Penduduk</a>
            <a href="surat_masuk.php"><i class="fas fa-inbox"></i> Surat Masuk</a>
            <a href="surat_keluar.php"><i class="fas fa-paper-plane"></i> Surat Keluar</a>
            <a href="pengajuan_surat.php"><i class="fas fa-paper-plane"></i> Pengajuan Surat</a>
            <a href="statistik.php"><i class="fas fa-chart-bar"></i> Statistik</a>
            <a href="laporan.php"><i class="fas fa-envelope"></i> Laporan </a>
        <?php } elseif($role == 'kepala_desa'){ ?>
            <a href="kepala_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="surat_keluar.php"><i class="fas fa-paper-plane"></i> Surat Keluar</a>
            <a href="pengajuan_surat.php"><i class="fas fa-envelope"></i> Pengajuan Surat</a>
        <a href="tanda_tangan.php"><i class="fas fa-signature"></i> Tanda Tangan </a>
        <?php } ?>
        <a class="logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</header>

<div class="container">
    <h1>Surat Keluar</h1>
    <!--<button class="add" onclick="openForm()">+ Tambah Surat Keluar</button>-->
    <br>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Jenis Surat</th>
                <th>File Surat</th>
                <th>Tanggal Upload</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row=mysqli_fetch_assoc($surat)){ ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['nik'] ?></td>
                <td><?= $row['jenis_surat'] ?></td>
                <td><?= $row['file_surat'] ?></td>
                <td><?= $row['tgl_upload'] ?></td>
                <td>
                    <?php if(isset($surat_file[$row['jenis_surat']])): ?>
                        <a class="view" href="<?= $surat_file[$row['jenis_surat']] ?>?id=<?= $row['id_pengajuan'] ?>" target="_blank"><i class="fas fa-file-pdf"></i> Lihat</a>
                    <?php else: ?>
                        <span style="color:red;">Template tidak tersedia</span>
                    <?php endif; ?>
                    <!--<button class="action edit" onclick="openForm('<?= $row['id'] ?>')"><i class="fas fa-edit"></i></button>
                    <button class="action delete" onclick="openDelete('<?= $row['id'] ?>')"><i class="fas fa-trash"></i></button>-->
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal" id="modal">
    <div class="modal-content">
        <button class="close" onclick="closeModal()">X</button>
        <h2>Form Surat Keluar</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id">
            <label>Pilih Pengajuan</label>
            <select name="id_pengajuan" required>
                <option value="">-- Pilih Pengajuan --</option>
                <?php
                $q = mysqli_query($conn,"SELECT * FROM pengajuan WHERE status='Disetujui' ORDER BY tgl_pengajuan DESC");
                while($r=mysqli_fetch_assoc($q)){
                    echo "<option value='{$r['id']}'>{$r['nama']} - {$r['jenis_surat']}</option>";
                }
                ?>
            </select>
            <label>Berkas (Opsional)</label>
            <input type="file" name="berkas">
            <button type="submit" name="add" class="save">Tambah</button>
            <button type="submit" name="edit" class="save">Simpan</button>
        </form>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal" id="deleteModal">
    <div class="modal-content">
        <button class="close" onclick="closeModal()">X</button>
        <h2>Konfirmasi Hapus</h2>
        <p>Apakah Anda yakin ingin menghapus surat keluar ini?</p>
        <form method="POST">
            <input type="hidden" name="id">
            <button type="submit" name="delete" class="del">Hapus</button>
            <button type="button" class="del" onclick="closeModal()" style="background:#3498db;">Batal</button>
        </form>
    </div>
</div>

<footer>
    &copy; <?=date('Y')?> Sistem Surat Desa Palewai. All rights reserved.
</footer>

</body>
</html>
