<?php
session_start();
include 'config.php';

$msg = '';
$icon = '';

// Ambil logo dari tabel settings
$logo = "icon.png"; // default
$result = mysqli_query($conn, "SELECT logo FROM settings LIMIT 1");
if($row = mysqli_fetch_assoc($result)){
    if(!empty($row['logo'])){
        $logo = "uploads/".$row['logo']; // pastikan folder uploads sesuai
    }
}

// Proses login
if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1";
    $res = mysqli_query($conn, $sql);

    if(mysqli_num_rows($res) > 0){
        $user = mysqli_fetch_assoc($res);
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $msg = "Login berhasil!";
        $icon = "✅";

        if($user['role']=='admin') header("refresh:1;url=admin_dashboard.php");
        elseif($user['role']=='petugas') header("refresh:1;url=petugas_dashboard.php");
        elseif($user['role']=='kepala_desa') header("refresh:1;url=kepala_dashboard.php");
    } else {
        $msg = "Username atau Password salah!";
        $icon = "❌";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Sistem Surat</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f4f7f8;}
header{
    background:#50C878;color:white;padding:15px 40px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 5px rgba(0,0,0,0.1);
}
header .logo span{font-weight:700;font-size:1.5rem;}
nav a{color:white;text-decoration:none;margin-left:25px;font-weight:600;padding:8px 12px;border-radius:5px;transition:0.3s;}
nav a:hover{background-color:white;color:#50C878;}
main{display:flex;justify-content:center;align-items:center;height:calc(100vh - 70px);}
.container{
    background:white;
    max-width:400px;
    width:100%;
    padding:40px 30px;
    border-radius:15px;
    box-shadow:0 15px 25px rgba(0,0,0,0.2);
    text-align:center;
    position: relative;
    transition: transform 0.3s, box-shadow 0.3s;
}
.container:hover{
    transform: translateY(-5px);
    box-shadow:0 25px 35px rgba(0,0,0,0.3);
}
h2{margin-bottom:25px;color:#2c3e50;}
input[type="text"], input[type="password"]{
    width:100%;
    padding:12px;
    margin:10px 0 20px 0;
    border-radius:8px;
    border:1px solid #ccc;
    transition: 0.3s;
}
input[type="text"]:focus, input[type="password"]:focus{
    border-color:#50C878;
    box-shadow:0 0 5px rgba(80,200,120,0.5);
    outline:none;
}
button{
    background:#50C878;
    color:white;
    padding:12px 25px;
    border:none;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
    width:100%;
}
button:hover{
    background:#46b06b;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.msg{
    text-align:center;
    margin-bottom:15px;
    font-weight:600;
    color:#2c3e50;
    font-size:1rem;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:10px;
}
.icon-msg{font-size:1.3rem;}
</style>
</head>
<body>

<header>
    <div class="logo" style="display:flex;align-items:center;">
        <img src="<?= $logo ?>" alt="Logo" style="height:42px;margin-right:12px;">
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

<main>
<div class="container">
    <h2>Login Sistem Surat</h2>

    <?php if($msg != '') echo '<div class="msg"><span class="icon-msg">'.$icon.'</span> '.$msg.'</div>'; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>
</main>

</body>
</html>
