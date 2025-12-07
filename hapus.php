<?php
require_once "functions.php";

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Gagal menghapus istilah.'
];

// Kita pakai POST untuk hapus agar lebih aman
if (isset($_POST["id"])) {
    $id = (int)$_POST["id"];
    $result = hapus($id); // Panggil fungsi hapus() yang sudah direvisi

    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'Istilah berhasil dihapus!';
    }
} else {
     $response['message'] = 'ID tidak ditemukan.';
}

echo json_encode($response);
?>