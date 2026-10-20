<?php
session_start();
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items = json_decode($_POST['items'], true);
    $total_bayar = floatval($_POST['total_bayar']);
    $jumlah_uang = floatval($_POST['jumlah_uang']);
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $id_user = $_SESSION['user_id'] ?? 1;

    if (empty($items) || $jumlah_uang < $total_bayar) {
        header("Location: index.php?status=gagal");
        exit;
    }

    $kembalian = $jumlah_uang - $total_bayar;
    $kode_transaksi = 'TRX-' . date('YmdHis');

    try {
        $conn->beginTransaction();

        $stmtPenjualan = $conn->prepare("INSERT INTO penjualan (kode_transaksi, id_user, total_bayar, jumlah_uang, kembalian, metode_pembayaran) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtPenjualan->execute([$kode_transaksi, $id_user, $total_bayar, $jumlah_uang, $kembalian, $metode_pembayaran]);
        
        $id_penjualan = $conn->lastInsertId();

        $stmtDetail = $conn->prepare("INSERT INTO detail_penjualan (id_penjualan, id_menu, jumlah, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmtStok = $conn->prepare("UPDATE menu SET stok = stok - ? WHERE id_menu = ?");

        foreach ($items as $item) {
            $subtotal = $item['harga'] * $item['jumlah'];
            $stmtDetail->execute([$id_penjualan, $item['id'], $item['jumlah'], $item['harga'], $subtotal]);
            $stmtStok->execute([$item['jumlah'], $item['id']]);
        }

        $conn->commit();
        header("Location: ../../cetak_struk.php?id=" . $id_penjualan);
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        die("Terjadi kesalahan transaksi: " . $e->getMessage());
    }
}
?>