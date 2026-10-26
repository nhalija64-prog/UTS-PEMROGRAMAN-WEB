<?php
require_once __DIR__ . '/connection.php';
mysqli_select_db($conn, "dbnilai");

$nim = mysqli_real_escape_string($conn, $_POST['nim']);
$nm_lengkap = mysqli_real_escape_string($conn, $_POST['nm_lengkap']);
$nilai_akhir = floatval($_POST['nilai_akhir']);

function getNilaiHuruf($nilai) {
    if ($nilai >= 0 && $nilai <= 34.50) {
        return ["E", "Tidak Lulus"];
    } elseif ($nilai > 34.50 && $nilai <= 50.50) {
        return ["D", "Kurang Memuaskan"];
    } elseif ($nilai > 50.50 && $nilai <= 65.50) {
        return ["C", "Cukup Memuaskan"];
    } elseif ($nilai > 65.50 && $nilai <= 80.50) {
        return ["B", "Memuaskan"];
    } elseif ($nilai > 80.50 && $nilai <= 100.00) {
        return ["A", "Sangat Memuaskan"];
    } else {
        return ["Invalid", "Nilai tidak valid"];
    }
}

$hasil = getNilaiHuruf($nilai_akhir);
$nilai_huruf = $hasil[0];
$predikat = $hasil[1];

// Upload Foto
$foto_nama = "";
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['foto']['tmp_name'];
    $fileName = $_FILES['foto']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = time() . '_' . $nim . '.' . $fileExtension;
        $uploadFileDir = './uploads/';
        
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }
        
        $dest_path = $uploadFileDir . $newFileName;
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $foto_nama = $newFileName;
        }
    }
}

$sql = "INSERT INTO nilai (nim, nm_lengkap, foto, nilai_akhir, nilai_huruf, predikat, created_date, modified_date)
        VALUES ('$nim', '$nm_lengkap', '$foto_nama', '$nilai_akhir', '$nilai_huruf', '$predikat', NOW(), NOW())";

if (mysqli_query($conn, $sql)) {
    header('Location: viewdata.php');
    exit();
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>