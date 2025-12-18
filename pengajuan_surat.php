<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];

// PROSES UPDATE STATUS (TIDAK BERUBAH)
if(isset($_POST['update_status'])){
    $id = intval($_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "UPDATE pengajuan SET status='$status' WHERE id=$id");

    if($status == "Disetujui"){
        $q = mysqli_query($conn, "SELECT * FROM pengajuan WHERE id=$id");
        $d = mysqli_fetch_assoc($q);

        $file_surat = "Surat_".str_replace(" ", "_", $d['jenis_surat'])."_ID".$id.".pdf";

        mysqli_query($conn, "
            INSERT INTO surat_keluar (id_pengajuan, file_surat)
            VALUES ($id, '$file_surat')
        ");
    }

    echo "<script>alert('Status berhasil diperbarui!'); window.location='pengajuan_surat.php';</script>";
}

// PROSES SIMPAN TANGGAPAN (TANGGAPAN diubah menjadi Catatan Umpan Balik)
if(isset($_POST['simpan_tanggapan'])){
    $id = intval($_POST['id_tanggapan']);
    // Variabel tanggapan dipertahankan namanya, namun isinya kini adalah teks bebas
    $tanggapan = mysqli_real_escape_string($conn, $_POST['tanggapan']); 

    mysqli_query($conn, "UPDATE pengajuan SET tanggapan='$tanggapan' WHERE id=$id");

    echo "<script>alert('Catatan Umpan Balik berhasil disimpan!'); window.location='pengajuan_surat.php';</script>";
}

$data = mysqli_query($conn, "SELECT * FROM pengajuan ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pengajuan Surat</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
<style>
/* ... (Bagian Style CSS tidak berubah) ... */
*{margin:0;padding:0;box-sizing:border-box;font-family:"Poppins",sans-serif;}
body{background:#f4f7f8;}
header{
    background:#50C878;
    padding:10px 40px;
    color:white;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);
}
header .logo{display:flex; align-items:center;}
header .logo img{height:42px;margin-right:10px;}
header nav a{
    color:white; margin-left:20px; text-decoration:none; font-weight:600;
    padding:8px 14px; border-radius:5px; transition:0.3s;
}
header nav a:hover{background:white;color:#50C878;}
header nav .logout{background:#e74c3c;}
header nav .logout:hover{background:#c0392b;color:white;}

.container{width:95%;margin:auto;margin-top:30px;}
h1{text-align:center;margin-bottom:20px;color:#2c3e50;}

table{width:100%;border-collapse:collapse;background:white;margin-top:20px;}
table th,table td{padding:10px;border:1px solid #ddd;text-align:center;}
table th{background:#50C878;color:white;}

.btn{padding:6px 10px;border:none;border-radius:5px;cursor:pointer;color:white;font-size:14px;margin:2px;}
.edit{background:#f1c40f;}
.download{background:#3498db;}

.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);}
.modal-content{background:white;width:90%;max-width:600px;margin:5% auto;padding:20px;border-radius:10px;text-align:center;overflow-y:auto;max-height:90%;}
.close{float:right;font-size:22px;cursor:pointer;}
select{width:100%;padding:10px;border-radius:5px;margin-top:10px;}
.btn-save{background:#50C878;margin-top:10px;width:100%;}

/* Footer */
footer{background:white;color:#50C878;text-align:center;padding:15px 20px;font-size:0.9rem;border-top:2px solid #50C878;margin-top:auto;}
footer a{color:#50C878; text-decoration:none;}
</style>
</head>
<body>

<header>
    <div class="logo"><img src="icon.png"><span>Surat Menyurat</span></div>
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
    <h1><i class="fas fa-envelope"></i> Pengajuan Surat</h1>
    <table>
        <tr>
            <th>ID</th><th>Nama</th><th>NIK</th><th>TTL</th><th>Agama</th><th>JK</th><th>Pekerjaan</th>
            <th>Alamat</th><th>Jenis Surat</th><th>Status</th><th>Aksi</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?=$row['id']?></td>
            <td>
                <a href="#" style="color:blue; text-decoration:underline;" onclick='openDetailModal(<?=json_encode($row)?>)'>
                    <?=$row['nama']?>
                </a>
            </td>
            <td><?=$row['nik']?></td>
            <td><?=$row['ttl']?></td>
            <td><?=$row['agama']?></td>
            <td><?=$row['jk']?></td>
            <td><?=$row['pekerjaan']?></td>
            <td><?=$row['alamat']?></td>
            <td><?=$row['jenis_surat']?></td>
            <td>
                <?php
                    if ($row['status']=="Menunggu") echo "<span style='color:#e67e22;'>Menunggu</span>";
                    elseif ($row['status']=="Proses") echo "<span style='color:#3498db;'>Proses</span>";
                    else echo "<span style='color:#27ae60;'>Disetujui</span>";
                ?>
            </td>
            <td>
                <button class="btn edit" onclick="openStatusModal(<?=$row['id']?>,'<?=$row['status']?>')"><i class="fas fa-edit"></i></button>
                <?php if($row['status']=="Disetujui"){
                    $id_pengajuan=$row['id']; $jenis_surat=$row['jenis_surat'];
                    $file_php="";
                    switch($jenis_surat){
                        case "Surat Tidak Mampu": $file_php="surat/surat_tidak_mampu.php?id=$id_pengajuan"; break;
                        case "Surat Domisili": $file_php="surat/surat_domisili.php?id=$id_pengajuan"; break;
                        case "Surat Keterangan Usaha": $file_php="surat/surat_usaha.php?id=$id_pengajuan"; break;
                        default: $file_php="surat/surat_default.php?id=$id_pengajuan"; break;
                    }
                    echo '<a class="btn download" href="'.$file_php.'" target="_blank"><i class="fas fa-download"></i></a>';
                } ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<div class="modal" id="modalStatus">
<div class="modal-content">
    <span class="close" onclick="document.getElementById('modalStatus').style.display='none'">&times;</span>
    <h3>Ubah Status Pengajuan</h3>
    <form method="POST">
        <input type="hidden" name="id" id="id_pengajuan">
        <select name="status" id="status_select"></select>
        <button type="submit" name="update_status" class="btn btn-save">Simpan</button>
    </form>
</div>
</div>

<div class="modal" id="modalDetail">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modalDetail').style.display='none'">&times;</span>
        <h3>Detail Pengajuan</h3>
        <form method="POST">
            <input type="hidden" name="id_tanggapan" id="id_tanggapan">
            <div id="detailContent" style="text-align:left; margin-top:15px;"></div>
            
            <label><b>Catatan Umpan Balik (untuk Warga):</b></label>
            <textarea name="tanggapan" id="tanggapan_textarea" rows="4" style="width:100%; padding:8px; margin-top:5px; border-radius:5px; border:1px solid #ccc;" placeholder="Contoh: Berkas KTP harus diunggah ulang karena buram, atau: Berkas sudah lengkap dan sedang diproses."></textarea>
            <button type="submit" name="simpan_tanggapan" class="btn btn-save">Simpan Catatan</button>
        </form>
    </div>
</div>

<script>
function openStatusModal(id, current){
    document.getElementById("modalStatus").style.display="block";
    document.getElementById("id_pengajuan").value=id;
    var role="<?= $role ?>";
    var select=document.getElementById("status_select");
    select.innerHTML="";
    if(role==="admin") ["Menunggu","Proses","Disetujui"].forEach(s=>{select.innerHTML+=`<option value="${s}" ${current==s?"selected":""}>${s}</option>`;});
    if(role==="petugas") ["Menunggu","Proses"].forEach(s=>{select.innerHTML+=`<option value="${s}" ${current==s?"selected":""}>${s}</option>`;});
    if(role==="kepala_desa") select.innerHTML=`<option value="Disetujui" ${current=="Disetujui"?"selected":""}>Disetujui</option>`;
}

function openDetailModal(data){
    document.getElementById('modalDetail').style.display='block';
    document.getElementById('id_tanggapan').value=data.id;
    
    // Memasukkan tanggapan yang sudah ada ke dalam textarea
    document.getElementById('tanggapan_textarea').value = data.tanggapan ? data.tanggapan : ""; 

    var html="<p><b>Nama:</b> "+data.nama+"</p>";
    html+="<p><b>NIK:</b> "+data.nik+"</p>";
    html+="<p><b>TTL:</b> "+data.ttl+"</p>";
    html+="<p><b>Agama:</b> "+data.agama+"</p>";
    html+="<p><b>JK:</b> "+data.jk+"</p>";
    html+="<p><b>Pekerjaan:</b> "+data.pekerjaan+"</p>";
    html+="<p><b>Alamat:</b> "+data.alamat+"</p>";
    html+="<p><b>Jenis Surat:</b> "+data.jenis_surat+"</p>";
    html+="<p><b>Status:</b> "+data.status+"</p>";
    // Tanggapan sudah ditampilkan di textarea, jadi ini bisa dihilangkan atau dipertahankan
    // html+="<p><b>Tanggapan:</b> "+(data.tanggapan?data.tanggapan:"Belum ada")+"</p>"; 

    var files=[];
    try{ files=JSON.parse(data.berkas); } catch(e){ files=[data.berkas]; }

    if(files.length>0){
        html+="<p><b>Berkas:</b></p>";
        files.forEach(function(f){
            var ext=f.split('.').pop().toLowerCase();
            if(ext==="pdf") html+='<iframe src="uploads/'+f+'" width="100%" height="300px" style="margin-bottom:10px;"></iframe>';
            else if(["jpg","jpeg","png","gif"].includes(ext)) html+='<img src="uploads/'+f+'" style="max-width:100%;max-height:300px;margin-bottom:10px;">';
            else html+='<p><a href="uploads/'+f+'" target="_blank">Download '+f+'</a></p>';
        });
    }

    document.getElementById('detailContent').innerHTML=html;
}
</script>

<footer>&copy; <?=date('Y')?> Sistem Surat Desa Palewai. All rights reserved.</footer>
</body>
</html>
