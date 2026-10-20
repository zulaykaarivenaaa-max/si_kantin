<?php
session_start();
require_once '../../config/koneksi.php';

$stmt = $conn->prepare("SELECT menu.*, kategori.nama_kategori FROM menu JOIN kategori ON menu.id_kategori = kategori.id_kategori ORDER BY menu.id_menu DESC");
$stmt->execute();
$menus = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Menu - SI Kantin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Kelola Menu Kantin</h2>
            <div>
                <a href="tambah.php" class="btn btn-oranye">+ Tambah Menu</a>
                <a href="../kasir/index.php" class="btn btn-secondary">Kembali ke Kasir</a>
            </div>
        </div>
        <div class="card card-custom p-3">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($menus as $m): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($m['nama_menu']) ?></td>
                        <td><?= htmlspecialchars($m['nama_kategori']) ?></td>
                        <td>Rp <?= number_format($m['harga'],0,',','.') ?></td>
                        <td><?= $m['stok'] ?></td>
                        <td>
                            <a href="edit.php?id=<?= $m['id_menu'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>