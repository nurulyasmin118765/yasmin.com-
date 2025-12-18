<?php
include 'config.php';

if(isset($_POST['query'])){
    $query = mysqli_real_escape_string($conn, $_POST['query']);
    $data = [];

    $sql = "SELECT * FROM penduduk WHERE nama LIKE '%$query%' LIMIT 10";
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($result)){
        $data[] = [
            'nama' => $row['nama'],
            'nik' => $row['nik'],
            'ttl' => $row['ttl'],
            'agama' => $row['agama'],
            'jk' => $row['jk'],
            'pekerjaan' => $row['pekerjaan'],
            'alamat' => $row['alamat']
        ];
    }

    echo json_encode($data);
}
?>
