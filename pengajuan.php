<?php
session_start();
include 'config.php'; // Koneksi database

// Ambil logo dari tabel settings
$logo = "icon.png"; // default
$logoQuery = mysqli_query($conn,"SELECT logo FROM settings LIMIT 1");
if(mysqli_num_rows($logoQuery) > 0){
    $rowLogo = mysqli_fetch_assoc($logoQuery);
    if(!empty($rowLogo['logo'])){
        $logo = "uploads/".$rowLogo['logo']; 
    }
}

// Persyaratan surat
$syarat_surat = [
    "Surat Domisili" => [
        "Fotokopi KTP",
        "Fotokopi KK",
        "Surat Pengantar RT/RW"
    ],
    "Keterangan Kematian" => [
        "Fotokopi KTP almarhum",
        "Fotokopi KK almarhum",
        "Surat Keterangan Kematian dari RT/RW",
        "Formulir Permohonan"
    ],
    "Surat Usaha" => [
        "Fotokopi KTP",
        "Fotokopi KK",
        "Formulir Pengajuan Surat Usaha"
    ],
    "Surat Tidak Mampu" => [
        "Fotokopi KTP",
        "Formulir Permohonan Surat Tidak Mampu"
    ]
];

$showModal = false;
$msg = "";

// PROSES SUBMIT FORM
if(isset($_POST['submit_pengajuan'])){

    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $jenis_surat = mysqli_real_escape_string($conn, $_POST['jenis_surat']);
    $ttl = mysqli_real_escape_string($conn, $_POST['ttl']);
    $agama = mysqli_real_escape_string($conn, $_POST['agama']);
    $jk = mysqli_real_escape_string($conn, $_POST['jk']);
    $pekerjaan = mysqli_real_escape_string($conn, $_POST['pekerjaan']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    // UPLOAD MULTI BERKAS
    $uploaded_files = [];

    foreach($_FILES['berkas']['name'] as $i => $name){

        if(empty($name)) continue;

        $file_name = $_FILES['berkas']['name'][$i];
        $tmp_name  = $_FILES['berkas']['tmp_name'][$i];
        $size      = $_FILES['berkas']['size'][$i];

        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['pdf','jpg','jpeg','png','doc','docx'];

        if(!in_array($ext, $allowed)){
            $msg = "Format file tidak diperbolehkan.";
            break;
        }

        if($size > 5*1024*1024){
            $msg = "Ukuran file terlalu besar.";
            break;
        }

        $newname = time() . "_" . rand(1000,9999) . "_" . $file_name;

        $upload_dir = "uploads/";
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        move_uploaded_file($tmp_name, $upload_dir.$newname);

        $uploaded_files[] = $newname;
    }

    // Simpan ke database dalam satu kolom JSON
    $berkas_json = mysqli_real_escape_string($conn, json_encode($uploaded_files));

    $sql = "INSERT INTO pengajuan 
        (nama, nik, jenis_surat, berkas, status, tgl_pengajuan, ttl, agama, jk, pekerjaan, alamat)
        VALUES 
        ('$nama','$nik','$jenis_surat','$berkas_json','Menunggu',NOW(),'$ttl','$agama','$jk','$pekerjaan','$alamat')";

    if(mysqli_query($conn, $sql)){
        $msg = "Pengajuan berhasil dikirim.";
        $showModal = true;
    } else {
        $msg = "Terjadi kesalahan: ".mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengajuan Surat</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#ffffff;}
header{background:#50C878;color:white;padding:15px 40px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 5px rgba(0,0,0,0.1);}
header .logo img{height:42px;margin-right:12px;}
header .logo span{font-weight:700;font-size:1.5rem;}
nav a{color:white;text-decoration:none;margin-left:25px;font-weight:600;padding:8px 12px;border-radius:5px;transition:0.3s;}
nav a:hover{background-color:white;color:#50C878;}
.container{max-width:700px;margin:50px auto;padding:30px;border:1px solid #ddd;border-radius:10px;box-shadow:0 3px 6px rgba(0,0,0,0.1);}
h2{text-align:center;margin-bottom:25px;color:#2c3e50;}
form label{display:block;margin-bottom:8px;font-weight:600;}
form input[type="text"],form input[type="file"],form select, form textarea{width:100%;padding:10px;margin-bottom:20px;border:1px solid #ccc;border-radius:5px;}
form button{background:#50C878;color:white;padding:12px 25px;border:none;border-radius:5px;font-weight:600;cursor:pointer;}
form button:hover{background:#46b06b;}
.msg{ text-align:center; margin-bottom:20px; font-weight:600; color:green;}
#syarat{background:#f0f0f0;padding:10px;border-radius:5px;margin-bottom:20px;display:none;}
#uploadArea label{font-weight:600;margin-top:10px;}
.modal{display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);}
.modal-content{background:white;margin:15% auto;padding:20px;width:80%;max-width:400px;border-radius:10px;text-align:center;}
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

<main>
<div class="container" id="formPengajuan">
<h2>Form Pengajuan Surat</h2>

<?php if(!empty($msg)) echo '<div class="msg">'.$msg.'</div>'; ?>

<form action="" method="POST" enctype="multipart/form-data">

    <label>Nama Lengkap</label>
    <input type="text" name="nama" id="nama" required>

    <label>NIK</label>
    <input type="text" name="nik" id="nik" readonly required>

    <label>Tempat & Tanggal Lahir</label>
    <input type="text" name="ttl" id="ttl" required>

    <label>Agama</label>
    <select name="agama" id="agama" required>
        <option value="">-- Pilih Agama --</option>
        <option>Islam</option><option>Kristen</option>
        <option>Katolik</option><option>Hindu</option>
        <option>Buddha</option><option>Konghucu</option>
    </select>

    <label>Jenis Kelamin</label>
    <select name="jk" id="jk" required>
        <option value="">-- Pilih Jenis Kelamin --</option>
        <option>Laki-laki</option>
        <option>Perempuan</option>
    </select>

    <label>Pekerjaan</label>
    <input type="text" name="pekerjaan" id="pekerjaan" required>

    <label>Alamat</label>
    <textarea name="alamat" id="alamat" rows="3" required></textarea>

    <label>Jenis Surat</label>
    <select name="jenis_surat" id="jenis_surat" required>
        <option value="">-- Pilih Jenis Surat --</option>
        <?php 
        foreach($syarat_surat as $jenis => $syarat){
            echo "<option value='$jenis'>$jenis</option>";
        }
        ?>
    </select>

    <div id="syarat"></div>

    <!-- TEMPAT UPLOAD DINAMIS -->
    <div id="uploadArea"></div>

    <button type="submit" name="submit_pengajuan">Kirim Pengajuan</button>
</form>
</div>
</main>

<div id="modalNotif" class="modal">
    <div class="modal-content">
        <p>Pengajuan Berhasil Dikirim</p>
        <button onclick="$('#modalNotif').hide()">Tutup</button>
    </div>
</div>

<script>
$(document).ready(function(){

    var syaratSurat = <?php echo json_encode($syarat_surat, JSON_UNESCAPED_UNICODE); ?>;

    // AUTO FILL DATA
    $("#nama").autocomplete({
        source: function(request, response){
            $.ajax({
                url: "autocomplete_nik.php",
                method: "POST",
                data: {query: request.term},
                dataType: "json",
                success: function(data){
                    response(data.map(item => ({
                        label: item.nama,
                        value: item.nama,
                        detail: item
                    })));
                }
            });
        },
        select: function(event, ui){
            var d = ui.item.detail;
            $("#nik").val(d.nik);
            $("#ttl").val(d.ttl);
            $("#agama").val(d.agama);
            $("#jk").val(d.jk);
            $("#pekerjaan").val(d.pekerjaan);
            $("#alamat").val(d.alamat);
        },
        minLength: 1
    });

    // Generate Upload berdasarkan syarat surat
    $("#jenis_surat").change(function(){
        var jenis = $(this).val();
        $("#syarat").hide();
        $("#uploadArea").html("");

        if(jenis){
            var html = "<strong>Persyaratan:</strong><ul>";
            syaratSurat[jenis].forEach(function(item, index){
                html += "<li>"+item+"</li>";

                $("#uploadArea").append(`
                    <label>Upload ${item}</label>
                    <input type="file" name="berkas[]" required>
                `);
            });
            html += "</ul>";
            $("#syarat").html(html).show();
        }
    });

    <?php if($showModal){ ?>
        $("#modalNotif").show();
    <?php } ?>
});
</script>

</body>
</html>
