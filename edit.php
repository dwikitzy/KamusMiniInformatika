<?php
require_once "functions.php";

header('Content-Type: application/json');

// LOGIKA 1: Mengambil data untuk ditampilkan di modal (Method GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $data = getIstilahById($id); 
        
        if ($data) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak disediakan.']);
    }
}

// LOGIKA 2: Memproses update data (Method POST)
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $response = [
        'status' => 'error',
        'message' => 'Gagal mengubah istilah. (Unknown error)'
    ];

    if (!empty($_POST) && isset($_POST['id'])) {
        
        $result = ubah($_POST); // $result sekarang berisi 0, 1, atau 2
        
        if ($result === 1) { 
            $response['status'] = 'success';
            $response['message'] = 'Istilah berhasil diubah!';
        } else if ($result === 2) { 
            $response['status'] = 'success'; 
            $response['message'] = 'Tidak ada perubahan data.';
        } else { // Kode 0
            $response['status'] = 'error';
            $response['message'] = 'Gagal mengubah istilah. Terjadi kesalahan database.';
        }
        
    } else {
        $response['message'] = 'Data tidak lengkap.';
    }

    echo json_encode($response);
} 

// Jika method tidak didukung
else {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid.']);
}
?>