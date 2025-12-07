<?php
require_once "functions.php";

$i = 1;
// Kita akan selalu tampilkan semua istilah saat halaman pertama kali dibuka
$items = semuaIstilah();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kamus Informatika</title>
    
    <link rel="stylesheet" href="style.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container">
        
        <header>
            <h1>Kamus Mini Informatika 💻</h1>
        </header>

        <div class="controls-container">
            <a href="#" id="btn-tambah" class="btn btn-primary">Tambah Istilah Baru</a>
            
            <form action="" method="get" id="search-form">
                <input type="text" id="search-input" name="keyword" placeholder="Ketik untuk mencari istilah..."/>
            </form>
        </div>


        <main id="main-content">
            <div class="table-wrapper">
                <table class="kamus-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Istilah</th>
                            <th>Definisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="results-container">
                        <?php foreach ($items as $item): ?>
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
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div> </main>

    </div>

    <div id="modal-backdrop" class="modal-backdrop"></div>
    <div id="modal-container" class="modal-container">
        </div>
    
    <div id="toast-notification" class="toast"></div>

    <script src="script.js"></script>
</body>
</html>