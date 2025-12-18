<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'petugas'){
    header("Location: login.php");
    exit;
}
include 'config.php';

// Tambah surat
if(isset($_POST['add'])){
    $no_surat = mysqli_real_escape_string($conn,$_POST['no_surat']);
    $tanggal = mysqli_real_escape_string($conn,$_POST['tanggal']);
    $pengirim = mysqli_real_escape_string($conn,$_POST['pengirim']);
    $perihal = mysqli_real_escape_string($conn,$_POST['perihal']);
    $isi = mysqli_real_escape_string($conn,$_POST['isi']);

    if(isset($_FILES['berkas']) && $_FILES['berkas']['name'] != ''){
        $file = $_FILES['berkas'];
        $filename = time().'_'.$file['name'];
        move_uploaded_file($file['tmp_name'],'uploads/'.$filename);
    } else {
        $filename = NULL;
    }

    mysqli_query($conn,"INSERT INTO surat_masuk (no_surat,tanggal,pengirim,perihal,isi,berkas) VALUES ('$no_surat','$tanggal','$pengirim','$perihal','$isi','$filename')");
    header("Location: surat_masuk.php"); exit;
}

// Edit surat
if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $no_surat = mysqli_real_escape_string($conn,$_POST['no_surat']);
    $tanggal = mysqli_real_escape_string($conn,$_POST['tanggal']);
    $pengirim = mysqli_real_escape_string($conn,$_POST['pengirim']);
    $perihal = mysqli_real_escape_string($conn,$_POST['perihal']);
    $isi = mysqli_real_escape_string($conn,$_POST['isi']);

    if(isset($_FILES['berkas']) && $_FILES['berkas']['name'] != ''){
        $file = $_FILES['berkas'];
        $filename = time().'_'.$file['name'];
        move_uploaded_file($file['tmp_name'],'uploads/'.$filename);
        $berkas_sql = ", berkas='$filename'";
    } else { $berkas_sql = ""; }

    mysqli_query($conn,"UPDATE surat_masuk SET no_surat='$no_surat', tanggal='$tanggal', pengirim='$pengirim', perihal='$perihal', isi='$isi' $berkas_sql WHERE id=$id");
    header("Location: surat_masuk.php"); exit;
}

// Hapus surat
if(isset($_POST['delete'])){
    $id = intval($_POST['id']);
    $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT berkas FROM surat_masuk WHERE id=$id"));
    if($row['berkas']) @unlink('uploads/'.$row['berkas']);
    mysqli_query($conn,"DELETE FROM surat_masuk WHERE id=$id");
    header("Location: surat_masuk.php"); exit;
}

// Ambil data surat
$surat = mysqli_query($conn,"SELECT * FROM surat_masuk ORDER BY tanggal DESC");

// AJAX untuk edit
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM surat_masuk WHERE id=$id"));
    echo json_encode($row);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Surat Masuk</title>
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

/* Modal */
.modal{display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);}
.modal-content{background:white;margin:50px auto;padding:20px;width:90%;max-width:500px;border-radius:10px;position:relative;}
.modal-content h2{margin-bottom:20px;}
.modal-content input, .modal-content select, .modal-content textarea{width:100%;padding:10px;margin-bottom:15px;border:1px solid #ccc;border-radius:5px;}
.modal-content button.close{position:absolute;top:10px;right:10px;background:#e74c3c;color:white;padding:5px 10px;border:none;border-radius:5px;cursor:pointer;}
.modal-content button.save{background:#50C878;color:white;padding:10px 15px;border:none;border-radius:5px;cursor:pointer;margin-right:10px;}
.modal-content button.del{background:#e74c3c;color:white;padding:10px 15px;border:none;border-radius:5px;}
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
        $.get('surat_masuk.php?id='+id,function(data){
            let row = JSON.parse(data);
            $('#modal input[name="id"]').val(row.id);
            $('#modal input[name="no_surat"]').val(row.no_surat);
            $('#modal input[name="tanggal"]').val(row.tanggal);
            $('#modal input[name="pengirim"]').val(row.pengirim);
            $('#modal input[name="perihal"]').val(row.perihal);
            $('#modal textarea[name="isi"]').val(row.isi);
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
    <h1>Surat Masuk</h1>
    <button class="add" onclick="openForm()">+ Tambah Surat</button>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>No Surat</th>
                <th>Tanggal</th>
                <th>Pengirim</th>
                <th>Perihal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row=mysqli_fetch_assoc($surat)){ ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['no_surat'] ?></td>
                <td><?= $row['tanggal'] ?></td>
                <td><?= $row['pengirim'] ?></td>
                <td><?= $row['perihal'] ?></td>
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
        <h2>Form Surat Masuk</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id">
            <label>No Surat</label>
            <input type="text" name="no_surat" required>
            <label>Tanggal</label>
            <input type="date" name="tanggal" required>
            <label>Pengirim</label>
            <input type="text" name="pengirim" required>
            <label>Perihal</label>
            <input type="text" name="perihal" required>
            <label>Isi</label>
            <textarea name="isi" rows="3"></textarea>
            <label>Berkas</label>
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
        <p>Apakah Anda yakin ingin menghapus surat ini?</p>
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
