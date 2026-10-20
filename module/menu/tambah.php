<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

require_once '../../config/koneksi.php';

// Ambil daftar kategori dari database
$stmtKategori = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$kategori = $stmtKategori->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_menu   = trim($_POST['nama_menu']);
    $id_kategori = intval($_POST['id_kategori']);
    $harga       = floatval($_POST['harga']);
    $stok        = intval($_POST['stok']);
    
    $nama_gambar = 'default.jpg';
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['gambar']['tmp_name'];
        $fileName      = $_FILES['gambar']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            // Beri nama unik menggunakan timestamp agar tidak bentrok
            $nama_gambar = time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadFileDir = '../../assets/images/';
            
            // Pastikan direktori tujuan ada
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $nama_gambar;
            if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                $error = 'Gagal mengunggah gambar menu. Menggunakan gambar default.';
                $nama_gambar = 'default.jpg';
            }
        } else {
            $error = 'Format file tidak didukung! Gunakan format JPG, JPEG, PNG, atau WEBP.';
        }
    }

    if (empty($error)) {
        try {
            $stmt = $conn->prepare("INSERT INTO menu (id_kategori, nama_menu, harga, stok, gambar, status) VALUES (?, ?, ?, ?, ?, 'tersedia')");
            $stmt->execute([$id_kategori, $nama_menu, $harga, $stok, $nama_gambar]);
            
            header("Location: index.php?status=sukses_tambah");
            exit;
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan database: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI Kantin - Tambah Menu Baru</title>
    <!-- Bootstrap 5 & FontAwesome CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-dark navbar-kantin mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fa-solid fa-utensils me-2"></i>SI KANTIN
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="../kasir/index.php" class="text-white text-decoration-none small fw-semibold">
                    <i class="fa-solid fa-cash-register me-1"></i> Kasir POS
                </a>
                <a href="index.php" class="text-white text-decoration-none small fw-semibold">
                    <i class="fa-solid fa-list-check me-1"></i> Kelola Menu
                </a>
                <span class="text-white small">|</span>
                <span class="text-white small">
                    <i class="fa-solid fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?>
                </span>
                <a href="../../logout.php" class="btn btn-sm btn-light text-danger fw-bold ms-2">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Form Container -->
    <div class="container my-4" style="max-width: 600px;">
        <div class="card card-custom p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-secondary m-0">
                    <i class="fa-solid fa-folder-plus me-2"></i>Tambah Menu Baru
                </h5>
                <a href="index.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            
            <hr class="mt-1 mb-4">

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 small" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="tambah.php" method="POST" enctype="multipart/form-data">
                
                <!-- Nama Menu -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Nama Menu / Makanan</label>
                    <input type="text" name="nama_menu" class="form-control" placeholder="Contoh: Es Teh Manis / Ayam Geprek" required>
                </div>

                <!-- Kategori Menu -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Kategori</label>
                    <select name="id_kategori" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= $k['id_kategori'] ?>">
                                <?= htmlspecialchars($k['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Row Harga & Stok -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="10000" min="0" step="500" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Stok Awal</label>
                        <input type="number" name="stok" class="form-control" placeholder="50" min="0" required>
                    </div>
                </div>

                <!-- Upload Gambar -->
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Foto Menu (Opsional)</label>
                    <input type="file" name="gambar" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                    <div class="form-text text-muted small">Format: JPG, PNG, WEBP. Maksimum gambar otomatis disesuaikan.</div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-oranye w-100 py-2 fw-semibold">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Menu
                </button>

            </form>
        </div>
    </div>

</body>
</html>