<?php
require_once "functions.php";

$i = 1;
$keyword = $_GET["keyword"] ?? ''; // Pakai null coalescing operator

// Logika pencarian/tampil semua
if (!empty($keyword)) {
    $items = cari($keyword);
} else {
    // Jika search box kosong, tampilkan semua lagi
    $items = semuaIstilah();
}

// Cek jika hasil kosong
if (empty($items)) {
    echo '<tr><td colspan="4" style="text-align: center; padding: 2rem; font-style: italic;">Istilah tidak ditemukan.</td></tr>';
} else {
    // Loop dan cetak HANYA <tr> nya saja
    foreach ($items as $item): ?>
        <tr>
            <td><?= $i ?></td>
            <td data-label="Istilah"><?= $item["istilah"] ?></td>
            <td data-label="Definisi"><?= $item["definisi"] ?></td>
            <td data-label="Aksi">
                <button class="btn btn-edit" data-id="<?= $item['id'] ?>">Edit</button>
                <button class="btn btn-hapus" data-id="<?= $item['id'] ?>">Hapus</button>
            </td>
        </tr>
        <?php $i++; ?>
    <?php endforeach;
}
?>