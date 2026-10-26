<?php
require_once __DIR__ . "/connection.php";
mysqli_select_db($conn, "dbnilai");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['namalengkap']);
    $nilai_akhir = floatval($_POST['nilaiakhir']);
    $modified_date = date('Y-m-d H:i:s');

    // Hitung ulang huruf & predikat
    function getNilaiHuruf($nilai) {
        if ($nilai >= 0 && $nilai <= 34.50) return ["E", "Tidak Lulus"];
        elseif ($nilai > 34.50 && $nilai <= 50.50) return ["D", "Kurang Memuaskan"];
        elseif ($nilai > 50.50 && $nilai <= 65.50) return ["C", "Cukup Memuaskan"];
        elseif ($nilai > 65.50 && $nilai <= 80.50) return ["B", "Memuaskan"];
        elseif ($nilai > 80.50 && $nilai <= 100.00) return ["A", "Sangat Memuaskan"];
        else return ["Invalid", "Nilai tidak valid"];
    }

    $hasil = getNilaiHuruf($nilai_akhir);
    $nilai_huruf = $hasil[0];
    $predikat = $hasil[1];

    // Cek upload foto baru
    $foto_update = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = time() . '_' . $nim . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            
            if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                $foto_update = ", foto = '$newFileName'";
            }
        }
    }

    $sql = "UPDATE nilai SET 
            nm_lengkap = '$nama_lengkap', 
            nilai_akhir = '$nilai_akhir', 
            nilai_huruf = '$nilai_huruf', 
            predikat = '$predikat', 
            modified_date = '$modified_date'
            $foto_update 
            WHERE nim = '$nim'";

    if (mysqli_query($conn, $sql)) {
        header("Location: viewdata.php");
        exit();
    } else {
        echo "Gagal memperbarui data: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
?>