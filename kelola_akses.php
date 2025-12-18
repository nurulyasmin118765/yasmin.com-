<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}
include 'config.php';

// TAMBAH USER
if(isset($_POST['tambah'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']. PASSWORD_DEFAULT;
    $role = $_POST['role'];

    mysqli_query($conn, "INSERT INTO users (username, password, role, created_at) 
                         VALUES ('$username', '$password', '$role', NOW())");

    header("Location: kelola_akses.php?msg=add");
    exit;
}

// EDIT USER
if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];

    if($_POST['password'] != ""){
        $password = $_POST['password']. PASSWORD_DEFAULT;
        mysqli_query($conn, "UPDATE users SET username='$username', password='$password', role='$role' WHERE id=$id");
    } else {
        mysqli_query($conn, "UPDATE users SET username='$username', role='$role' WHERE id=$id");
    }

    header("Location: kelola_akses.php?msg=edit");
    exit;
}

// HAPUS USER
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: kelola_akses.php?msg=delete");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Akses Pengguna</title>

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
h1{color:#2c3e50;margin-bottom:10px;text-align:center;}
h2.welcome{font-size:1rem; color:#555; margin-bottom:40px; text-align:center;}

.cards{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    justify-content:center;
}
.card{
    background:white;
    border-radius:10px;
    padding:20px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
    width:220px;
    height:140px;
    text-align:center;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    transition:0.3s;
}
.card:hover{
    transform: translateY(-5px);
    box-shadow:0 5px 15px rgba(0,0,0,0.15);
}
.card h2{margin-bottom:10px;color:#50C878;font-size:1.1rem;}
.card p{font-size:1.5rem; color:#333; margin:0;}

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

.container{
    width:100%;
    max-width:1100px;
    background:white;
    padding:20px;
    margin:30px auto;
    border-radius:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}
table th, table td{
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
}
table th{background:#50C878;color:white;}

.btn{
    padding:8px 15px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-size:14px;
    margin:2px;
}
.btn-add{background:#27ae60;color:white;}
.btn-edit{background:#f39c12;color:white;}
.btn-del{background:#e74c3c;color:white;}

form input, form select{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    border:1px solid #ccc;
    border-radius:5px;
}

.modal{
    display:flex;
    justify-content:center;
    align-items:center;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(0,0,0,0.6);
}
.modal-content{
    width:400px;
    background:white;
    padding:20px;
    border-radius:10px;
}
.close{
    float:right;
    cursor:pointer;
    font-size:20px;
    color:#e74c3c;
}
.message{
    text-align:center;
    color:green;
    font-weight:bold;
    margin-bottom:10px;
}
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
    <h1>Kelola Akses Pengguna</h1>

    <div class="container">

        <?php
        if(isset($_GET['msg'])){
            if($_GET['msg']=='add') echo '<div class="message">Pengguna berhasil ditambahkan!</div>';
            if($_GET['msg']=='edit') echo '<div class="message">Pengguna berhasil diperbarui!</div>';
            if($_GET['msg']=='delete') echo '<div class="message">Pengguna berhasil dihapus!</div>';
        }
        ?>

        <button class="btn btn-add" onclick="document.getElementById('modalTambah').style.display='flex'">
            <i class="fa fa-plus"></i> Tambah Pengguna
        </button>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>

            <?php
            $users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
            while($row = mysqli_fetch_assoc($users)){
                echo "
                <tr>
                    <td>$row[id]</td>
                    <td>$row[username]</td>
                    <td>$row[role]</td>
                    <td>$row[created_at]</td>
                    <td>
                        <button class='btn btn-edit' onclick=\"editUser('$row[id]', '$row[username]', '$row[role]')\">Edit</button>
                        <a href='kelola_akses.php?hapus=$row[id]' onclick=\"return confirm('Yakin ingin menghapus?')\">
                            <button class='btn btn-del'>Hapus</button>
                        </a>
                    </td>
                </tr>";
            }
            ?>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modalTambah').style.display='none'">&times;</span>
        <h3>Tambah Pengguna</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
                <option value="kepala_desa">Kepala Desa</option>
            </select>
            <button type="submit" name="tambah" class="btn btn-add">Simpan</button>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modalEdit').style.display='none'">&times;</span>
        <h3>Edit Pengguna</h3>
        <form method="POST">
            <input type="hidden" name="id" id="edit_id">
            <input type="text" name="username" id="edit_username" required>
            <input type="password" name="password" placeholder="Kosongkan jika tidak diubah">
            <select name="role" id="edit_role" required>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
                <option value="kepala_desa">Kepala Desa</option>
            </select>
            <button type="submit" name="edit" class="btn btn-edit">Update</button>
        </form>
    </div>
</div>

<script>
function editUser(id, username, role){
    document.getElementById('modalEdit').style.display='flex';
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_role').value = role;
}
</script>

<footer>
    &copy; <?=date('Y')?> Sistem Surat Desa Palewai. All rights reserved.
</footer>

</body>
</html>
