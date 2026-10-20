<?php
session_start();
require_once '../../config/koneksi.php';

$stmt = $conn->query("SELECT p.*, u.nama as kasir FROM penjualan p JOIN users u ON p.id_user = u.id_user ORDER BY p.tanggal_transaksi DESC");
$laporan = $stmt->fetchAll();

$total_omzet = $conn->query("SELECT SUM(total_bayar) as total FROM penjualan")->fetch()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - SI Kantin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Laporan Penjualan</h2>
            <a href="../kasir/index.php" class="btn btn-secondary">Kembali ke Kasir</a>
        </div>

        <div class="card card-custom p-3 mb-4 bg-warning-subtle border-0">
            <h5>Total Omzet Penjualan</h5>
            <h3 class="fw-bold text-success">Rp <?= number_format($total_omzet,0,',','.') ?></h3>
        </div>

        <div class="card card-custom p-3">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Metode</th>
                        <th>Total Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($laporan as $l): ?>
                    <tr>
                        <td><?= $l['kode_transaksi'] ?></td>
                        <td><?= $l['tanggal_transaksi'] ?></td>
                        <td><?= htmlspecialchars($l['kasir']) ?></td>
                        <td><span class="badge bg-info text-dark"><?= strtoupper($l['metode_pembayaran']) ?></span></td>
                        <td>Rp <?= number_format($l['total_bayar'],0,',','.') ?></td>
                        <td>
                            <a href="../../cetak_struk.php?id=<?= $l['id_penjualan'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Struk</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>