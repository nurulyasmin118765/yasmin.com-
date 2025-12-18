<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'petugas'){
    header("Location: login.php");
    exit;
}
include 'config.php';

// Proses tambah data
if(isset($_POST['add'])){
    $nama = mysqli_real_escape_string($conn,$_POST['nama']);
    $nik = mysqli_real_escape_string($conn,$_POST['nik']);
    $ttl = mysqli_real_escape_string($conn,$_POST['ttl']);
    $agama = mysqli_real_escape_string($conn,$_POST['agama']);
    $jk = mysqli_real_escape_string($conn,$_POST['jk']);
    $pekerjaan = mysqli_real_escape_string($conn,$_POST['pekerjaan']);
    $alamat = mysqli_real_escape_string($conn,$_POST['alamat']);

    $sql = "INSERT INTO penduduk (nama, nik, ttl, agama, jk, pekerjaan, alamat) 
            VALUES ('$nama','$nik','$ttl','$agama','$jk','$pekerjaan','$alamat')";
    mysqli_query($conn,$sql);
    header("Location: data_penduduk.php");
    exit;
}

// Proses edit data
if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $nama = mysqli_real_escape_string($conn,$_POST['nama']);
    $nik = mysqli_real_escape_string($conn,$_POST['nik']);
    $ttl = mysqli_real_escape_string($conn,$_POST['ttl']);
    $agama = mysqli_real_escape_string($conn,$_POST['agama']);
    $jk = mysqli_real_escape_string($conn,$_POST['jk']);
    $pekerjaan = mysqli_real_escape_string($conn,$_POST['pekerjaan']);
    $alamat = mysqli_real_escape_string($conn,$_POST['alamat']);

    $sql = "UPDATE penduduk SET nama='$nama', nik='$nik', ttl='$ttl', agama='$agama', jk='$jk', pekerjaan='$pekerjaan', alamat='$alamat' WHERE id=$id";
    mysqli_query($conn,$sql);
    header("Location: data_penduduk.php");
    exit;
}

// Proses hapus data
if(isset($_POST['delete'])){
    $id = intval($_POST['id']);
    mysqli_query($conn,"DELETE FROM penduduk WHERE id=$id");
    header("Location: data_penduduk.php");
    exit;
}

$penduduk = mysqli_query($conn,"SELECT * FROM penduduk ORDER BY id DESC");

// Untuk AJAX (ambil data penduduk berdasarkan id)
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM penduduk WHERE id=$id"));
    echo json_encode($row);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Penduduk</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f4f7f8;}
header{
    background:#50C878;color:white;display:flex;justify-content:space-between;align-items:center;padding:10px 40px;box-shadow:0 2px 5px rgba(0,0,0,0.1);}
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

/* Modal */
.modal{display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);}
.modal-content{background:white;margin:50px auto;padding:20px;width:90%;max-width:500px;border-radius:10px;position:relative;}
.modal-content h2{margin-bottom:20px;}
.modal-content input, .modal-content select, .modal-content textarea{width:100%;padding:10px;margin-bottom:15px;border:1px solid #ccc;border-radius:5px;}
.modal-content button.close{position:absolute;top:10px;right:10px;background:#e74c3c;color:white;padding:5px 10px;border:none;border-radius:5px;cursor:pointer;}
.modal-content button.save{background:#50C878;color:white;padding:10px 15px;border:none;border-radius:5px;cursor:pointer;margin-right:10px;}
.modal-content button.del{background:#e74c3c;color:white;padding:10px 15px;border:none;border-radius:5px;cursor:pointer;}
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function openForm(id=''){
    if(id != ''){
        $.get('data_penduduk.php?id='+id,function(data){
            let row = JSON.parse(data);
            $('#modal input[name="id"]').val(row.id);
            $('#modal input[name="nama"]').val(row.nama);
            $('#modal input[name="nik"]').val(row.nik);
            $('#modal input[name="ttl"]').val(row.ttl);
            $('#modal select[name="agama"]').val(row.agama);
            $('#modal select[name="jk"]').val(row.jk);
            $('#modal input[name="pekerjaan"]').val(row.pekerjaan);
            $('#modal textarea[name="alamat"]').val(row.alamat);
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

<div class="container">
    <h1>Data Penduduk</h1>
    <button class="add" onclick="openForm()">+ Tambah Penduduk</button>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>TTL</th>
                <th>Agama</th>
                <th>JK</th>
                <th>Pekerjaan</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row=mysqli_fetch_assoc($penduduk)){ ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['nik'] ?></td>
                <td><?= $row['ttl'] ?></td>
                <td><?= $row['agama'] ?></td>
                <td><?= $row['jk'] ?></td>
                <td><?= $row['pekerjaan'] ?></td>
                <td><?= $row['alamat'] ?></td>
                <td>
                    <button class="action edit" onclick="openForm('<?= $row['id'] ?>')"><i class="fas fa-edit"></i></button>
                    <button class="action delete" onclick="openDelete('<?= $row['id'] ?>')"><i class="fas fa-trash"></i></button>
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
        <h2>Form Penduduk</h2>
        <form method="POST">
            <input type="hidden" name="id">
            <label>Nama</label>
            <input type="text" name="nama" required>
            <label>NIK</label>
            <input type="text" name="nik" required>
            <label>Tempat & Tanggal Lahir</label>
            <input type="text" name="ttl">
            <label>Agama</label>
            <select name="agama">
                <option value="">-- Pilih Agama --</option>
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Konghucu">Konghucu</option>
            </select>
            <label>Jenis Kelamin</label>
            <select name="jk">
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
            <label>Pekerjaan</label>
            <input type="text" name="pekerjaan">
            <label>Alamat</label>
            <textarea name="alamat" rows="3"></textarea>
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
        <p>Apakah Anda yakin ingin menghapus data ini?</p>
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
