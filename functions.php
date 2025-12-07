<?php
// --- UPDATE KONEKSI DATABASE (VERSI GETENV - LEBIH KUAT) ---

// Gunakan getenv() karena $_ENV seringkali dinonaktifkan di server cloud.
$host = getenv("MYSQLHOST") ?: 'localhost';
$user = getenv("MYSQLUSER") ?: 'root';
$pass = getenv("MYSQLPASSWORD") ?: '';
$db   = getenv("MYSQLDATABASE") ?: 'db_kamus';
$port = getenv("MYSQLPORT") ?: 3306;

$koneksi = new mysqli($host, $user, $pass, $db, $port);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
// --------------------------------------------------------
// --------------------------------------------------------

// Create
if (!function_exists("tambah")) {
    /**
     * Menambah data istilah baru.
     * REVISI: Mengembalikan int (0 = Gagal DB, 1 = Sukses, 2 = Duplikat)
     */
    function tambah($data): int
    {
        global $koneksi;

        $istilah = htmlspecialchars($data["istilah"]);
        $definisi = htmlspecialchars($data["definisi"]);

        // --- PERMINTAAN 2: CEK DUPLIKAT ---
        // Kita cek dulu apakah istilah sudah ada (perbandingan persis, case-sensitive)
        $checkQuery = $koneksi->prepare("SELECT id FROM table_istilah WHERE istilah = ?");
        $checkQuery->bind_param("s", $istilah);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            // Istilah sudah ada
            return 2; // 2 = GAGAL (Duplikat)
        }
        // ------------------------------------

        // Jika tidak duplikat, lanjutkan insert
        $query = $koneksi->prepare("INSERT INTO table_istilah (istilah, definisi, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $query->bind_param("ss", $istilah, $definisi);
        
        if ($query->execute() && $koneksi->affected_rows > 0) {
            return 1; // 1 = SUKSES
        } else {
            return 0; // 0 = GAGAL (Error database)
        }
    }
}

// Read (Semua Data)
if(!function_exists("semuaIstilah")){
    function semuaIstilah():array {
        global $koneksi;
        return $koneksi->query("SELECT * FROM table_istilah ORDER BY updated_at DESC")->fetch_all(MYSQLI_ASSOC);
    }
}

// Read (Satu Data by ID)
if(!function_exists("getIstilahById")){
    function getIstilahById($id): ?array {
        global $koneksi;
        
        $query = $koneksi->prepare("SELECT * FROM table_istilah WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $result = $query->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
}

// Update
if(!function_exists("ubah")) {
    // Mengembalikan int: 0 = Gagal, 1 = Sukses Berubah, 2 = Sukses Tidak Berubah
    function ubah($data): int {
        global $koneksi;

        $istilah = htmlspecialchars($data["istilah"]);
        $definisi = htmlspecialchars($data["definisi"]);
        $id = (int)$data["id"]; 

        $query = $koneksi->prepare("UPDATE table_istilah SET istilah = ?, definisi = ?, updated_at = NOW() WHERE id = ?");
        $query->bind_param("ssi", $istilah, $definisi, $id);

        if (!$query->execute()) {
            return 0; // 0 = GAGAL
        }

        if ($koneksi->affected_rows > 0) {
            return 1; // 1 = SUKSES (Ada yang berubah)
        } else {
            return 2; // 2 = SUKSES (Tidak ada yang berubah)
        }
    }
}

// Delete
if(!function_exists("hapus")){
    function hapus($id):bool { 
        global $koneksi;
        $id_int = (int)$id;

        $query = $koneksi->prepare("DELETE FROM table_istilah WHERE id = ?"); 
        $query->bind_param("i", $id_int); 
        $query->execute();

        return $koneksi->affected_rows > 0;
    }
}

// Cari
if (!function_exists("cari")) {
    function cari($keyword):array {
        global $koneksi;

        $keyword = "%$keyword%";
        
        // --- PERMINTAAN 1: HANYA CARI ISTILAH ---
        // Query diubah, hanya mencari berdasarkan 'istilah'
        $query = $koneksi->prepare("SELECT * FROM table_istilah WHERE istilah LIKE ? ORDER BY updated_at DESC");
        // Bind parameter diubah dari "ss" menjadi "s"
        $query->bind_param("s", $keyword);
        // ----------------------------------------
        
        $query->execute();

        return $query->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
