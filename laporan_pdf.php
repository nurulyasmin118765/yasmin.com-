<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'petugas'){
    header("Location: login.php");
    exit;
}

require('fpdf/fpdf.php');
include 'config.php';

$filter = $_GET['filter'] ?? 'harian';
$start  = $_GET['start'] ?? date('Y-m-d');
$end    = $_GET['end'] ?? date('Y-m-d');

switch($filter){
    case 'harian':
        $where = "DATE(tgl_pengajuan) = '$start'";
        $judul  = "Laporan Harian - " . date('d M Y', strtotime($start));
        break;
    case 'mingguan':
        $where = "DATE(tgl_pengajuan) BETWEEN '$start' AND '$end'";
        $judul  = "Laporan Mingguan - " . date('d M Y', strtotime($start)) . " s/d " . date('d M Y', strtotime($end));
        break;
    case 'bulanan':
        $where = "MONTH(tgl_pengajuan) = MONTH('$start') AND YEAR(tgl_pengajuan) = YEAR('$start')";
        $judul  = "Laporan Bulanan - " . date('M Y', strtotime($start));
        break;
    default:
        $where = "DATE(tgl_pengajuan) = '$start'";
        $judul  = "Laporan Harian - " . date('d M Y', strtotime($start));
}

$pengajuan = mysqli_query($conn, "SELECT * FROM pengajuan WHERE $where ORDER BY tgl_pengajuan ASC");
$surat_keluar = mysqli_query($conn, "SELECT sk.*, p.nama, p.jenis_surat 
    FROM surat_keluar sk 
    JOIN pengajuan p ON sk.id_pengajuan = p.id 
    WHERE $where ORDER BY sk.tgl_upload ASC");

class PDF extends FPDF{
    private $judul;
    
    function __construct($orientation='P', $unit='mm', $size='A4', $judul='Laporan Surat'){
        parent::__construct($orientation, $unit, $size);
        $this->judul = $judul;
    }
    
    function Header(){
        $this->SetFont('Arial','B',16);
        $this->SetTextColor(80, 200, 120);
        $this->Cell(0,12,$this->judul,0,1,'C');
        $this->Ln(5);
    }
}

$pdf = new PDF('P','mm','A4', $judul);
$pdf->AddPage();

// =====================
// Tabel Pengajuan
$pdf->SetFont('Arial','B',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(0,8,"Daftar Pengajuan",0,1);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(80,200,120);
$pdf->SetTextColor(255,255,255);

// Lebar kolom disesuaikan untuk rapi (total ~190mm untuk A4)
$header = ['ID'=>15,'Nama'=>65,'Jenis Surat'=>55,'Tanggal'=>25,'Status'=>30];
foreach($header as $col => $width){
    $pdf->Cell($width,8,$col,1,0,'C',true);
}
$pdf->Ln();

$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0,0,0);
$fill = false;

while($row = mysqli_fetch_assoc($pengajuan)){
    $pdf->SetFillColor(230, 245, 240);
    $pdf->Cell($header['ID'],8,$row['id'],1,0,'C',$fill);
    $pdf->Cell($header['Nama'],8,substr($row['nama'],0,35).(strlen($row['nama'])>35?'...':''),1,0,'L',$fill);
    $pdf->Cell($header['Jenis Surat'],8,substr($row['jenis_surat'],0,30).(strlen($row['jenis_surat'])>30?'...':''),1,0,'L',$fill);
    $pdf->Cell($header['Tanggal'],8,date('d-m-Y', strtotime($row['tgl_pengajuan'])),1,0,'C',$fill);

    if($row['status']=="Menunggu") $pdf->SetTextColor(230,126,34);
    elseif($row['status']=="Proses") $pdf->SetTextColor(52,152,219);
    else $pdf->SetTextColor(39,174,96);

    $pdf->Cell($header['Status'],8,$row['status'],1,1,'C',$fill);
    $pdf->SetTextColor(0,0,0);
    $fill = !$fill;
}

// =====================
// Tabel Surat Keluar
$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,"Daftar Surat Keluar",0,1);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(80,200,120);
$pdf->SetTextColor(255,255,255);

// Lebar kolom disesuaikan untuk rapi (total ~190mm untuk A4)
$header2 = ['ID'=>15,'Nama'=>60,'Jenis Surat'=>50,'Tanggal Upload'=>30,'File'=>35];
foreach($header2 as $col=>$w){
    $pdf->Cell($w,8,$col,1,0,'C',true);
}
$pdf->Ln();

$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0,0,0);
$fill=false;
while($row=mysqli_fetch_assoc($surat_keluar)){
    $pdf->SetFillColor(230,245,240);
    $pdf->Cell($header2['ID'],8,$row['id'],1,0,'C',$fill);
    $pdf->Cell($header2['Nama'],8,substr($row['nama'],0,30).(strlen($row['nama'])>30?'...':''),1,0,'L',$fill);
    $pdf->Cell($header2['Jenis Surat'],8,substr($row['jenis_surat'],0,25).(strlen($row['jenis_surat'])>25?'...':''),1,0,'L',$fill);
    $pdf->Cell($header2['Tanggal Upload'],8,date('d-m-Y', strtotime($row['tgl_upload'])),1,0,'C',$fill);
    $pdf->Cell($header2['File'],8,substr($row['file_surat'],0,18).(strlen($row['file_surat'])>18?'...':''),1,1,'L',$fill);
    $fill=!$fill;
}

// Footer
$pdf->SetY(-15);
$pdf->SetFont('Arial','I',8);
$pdf->SetTextColor(100,100,100);
$pdf->Cell(0,10,'Sistem Surat Desa Palewai - '.date('Y'),0,0,'C');

$pdf->Output("I","Laporan_Surat.pdf");
?>