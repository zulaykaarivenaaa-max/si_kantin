<?php
require_once 'config/koneksi.php';

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT p.*, u.nama as kasir FROM penjualan p JOIN users u ON p.id_user = u.id_user WHERE p.id_penjualan = ?");
$stmt->execute([$id]);
$trx = $stmt->fetch();

if (!$trx) {
    die("Transaksi tidak ditemukan!");
}

$stmtDetail = $conn->prepare("SELECT d.*, m.nama_menu FROM detail_penjualan d JOIN menu m ON d.id_menu = m.id_menu WHERE d.id_penjualan = ?");
$stmtDetail->execute([$id]);
$details = $stmtDetail->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - SI Kantin</title>
    <style>
        body { font-family: monospace; width: 280px; margin: auto; padding: 10px; background: #fff; }
        .text-center { text-align: center; }
        .line { border-bottom: 1px dashed #000; margin: 8px 0; }
        .flex { display: flex; justify-content: space-between; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <h3 style="margin: 0;">SI KANTIN</h3>
        <p style="font-size: 11px; margin: 2px 0;">Kantin Kejujuran / Kampus</p>
    </div>
    <div class="line"></div>
    <div style="font-size: 11px;">
        <div>Kode: <?= $trx['kode_transaksi'] ?></div>
        <div>Tgl : <?= $trx['tanggal_transaksi'] ?></div>
        <div>Kasir: <?= htmlspecialchars($trx['kasir']) ?></div>
    </div>
    <div class="line"></div>
    
    <?php foreach ($details as $item): ?>
        <div style="font-size: 11px;">
            <div><?= htmlspecialchars($item['nama_menu']) ?></div>
            <div class="flex">
                <span><?= $item['jumlah'] ?> x <?= number_format($item['harga_satuan'],0,',','.') ?></span>
                <span><?= number_format($item['subtotal'],0,',','.') ?></span>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="line"></div>
    <div style="font-size: 11px;">
        <div class="flex"><span>Total:</span><b>Rp <?= number_format($trx['total_bayar'],0,',','.') ?></b></div>
        <div class="flex"><span>Bayar (<?= strtoupper($trx['metode_pembayaran']) ?>):</span><span>Rp <?= number_format($trx['jumlah_uang'],0,',','.') ?></span></div>
        <div class="flex"><span>Kembali:</span><span>Rp <?= number_format($trx['kembalian'],0,',','.') ?></span></div>
    </div>
    <div class="line"></div>
    <p class="text-center" style="font-size: 10px;">Terima Kasih Atas Kunjungan Anda!</p>

    <div class="text-center no-print" style="margin-top: 15px;">
        <a href="module/kasir/index.php" style="font-family: sans-serif; text-decoration: none; background: #ff6b00; color: #fff; padding: 5px 10px; border-radius: 4px; font-size: 12px;">Kembali ke Kasir</a>
    </div>
</body>
</html>