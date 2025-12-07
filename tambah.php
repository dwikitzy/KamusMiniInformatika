<?php
require_once "functions.php";

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Gagal menambah istilah. (Unknown error)'
];

// Pastikan istilah dan definisi tidak kosong
if (!empty($_POST['istilah']) && !empty($_POST['definisi'])) {
    
    // Panggil fungsi tambah() yang sudah kita ubah
    $result = tambah($_POST); // $result sekarang berisi 0, 1, atau 2
    
    if ($result === 1) { 
        // SUKSES
        $response['status'] = 'success';
        $response['message'] = 'Istilah berhasil ditambah!';
    } else if ($result === 2) { 
        // GAGAL KARENA DUPLIKAT
        $response['status'] = 'error'; 
        $response['message'] = 'Gagal! Istilah "' . htmlspecialchars($_POST['istilah']) . '" sudah ada di database.';
    } else { 
        // GAGAL KARENA ERROR DATABASE (Kode 0)
        $response['status'] = 'error';
        $response['message'] = 'Gagal menambah istilah. Terjadi kesalahan database.';
    }
    
} else {
    $response['message'] = 'Data tidak lengkap. Istilah dan Definisi wajib diisi.';
}

echo json_encode($response);
?>