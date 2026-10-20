<?php
session_start();
require_once '../../config/koneksi.php';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM menu WHERE id_menu = ?");
$stmt->execute([$id]);
$menu = $stmt->fetch();

if (!$menu) header("Location: index.php");

$kategori = $conn->query("SELECT * FROM kategori")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_menu'];
    $id_kategori = $_POST['id_kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $stmtUpdate = $conn->prepare("UPDATE menu SET id_kategori = ?, nama_menu = ?, harga = ?, stok = ? WHERE id_menu = ?");
    $stmtUpdate->execute([$id_kategori, $nama, $harga, $stok, $id]);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu - SI Kantin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container my-4" style="max-width: 500px;">
        <div class="card card-custom p-4">
            <h4>Edit Menu</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Menu</label>
                    <input type="text" name="nama_menu" class="form-control" value="<?= htmlspecialchars($menu['nama_menu']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="id_kategori" class="form-select" required>
                        <?php foreach($kategori as $k): ?>
                            <option value="<?= $k['id_kategori'] ?>" <?= $k['id_kategori'] == $menu['id_kategori'] ? 'selected' : '' ?>><?= $k['nama_kategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" value="<?= $menu['harga'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" value="<?= $menu['stok'] ?>" required>
                </div>
                <button type="submit" class="btn btn-oranye w-100">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-link w-100 mt-2 text-secondary">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>