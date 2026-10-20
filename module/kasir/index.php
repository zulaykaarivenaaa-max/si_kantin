<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

require_once '../../config/koneksi.php';

// Ambil menu yang stoknya masih ada
$stmt = $conn->prepare("SELECT menu.*, kategori.nama_kategori FROM menu JOIN kategori ON menu.id_kategori = kategori.id_kategori WHERE stok > 0 ORDER BY nama_menu ASC");
$stmt->execute();
$menus = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI Kantin - Kasir POS</title>
    <!-- Bootstrap 5 & FontAwesome CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Style CSS Utama -->
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-dark navbar-kantin mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fa-solid fa-utensils me-2"></i>SI KANTIN
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="../menu/index.php" class="text-white text-decoration-none small fw-semibold"><i class="fa-solid fa-utensils me-1"></i> Kelola Menu</a>
                <a href="../laporan/index.php" class="text-white text-decoration-none small fw-semibold"><i class="fa-solid fa-file-invoice me-1"></i> Laporan</a>
                <span class="text-white small">|</span>
                <span class="text-white small"><i class="fa-solid fa-user me-1"></i> <?= htmlspecialchars($_SESSION['nama']) ?></span>
                <a href="../../logout.php" class="btn btn-sm btn-light text-danger fw-bold"><i class="fa-solid fa-sign-out-alt"></i></a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid px-4">
        <div class="row g-4">
            
            <!-- Daftar Menu (Kiri) -->
            <div class="col-md-7 col-lg-8">
                <div class="card card-custom p-3 mb-3">
                    <h5 class="fw-bold mb-3 text-secondary"><i class="fa-solid fa-list me-2"></i>Pilih Menu</h5>
                    
                    <div class="row row-cols-1 row-cols-md-3 g-3">
                        <?php foreach ($menus as $m): ?>
                        <?php 
                            // Cek jika gambar ada di folder assets/images
                            $foto = !empty($m['gambar']) && file_exists("../../assets/images/" . $m['gambar']) 
                                    ? $m['gambar'] 
                                    : 'default.jpg';
                        ?>
                        <div class="col">
                            <div class="card h-100 border-light-subtle shadow-sm p-2 text-center">
                                <!-- Tag Gambar dengan Class img-menu agar tidak terpotong -->
                                <img src="../../assets/images/<?= $foto ?>" 
                                     class="card-img-top img-menu" 
                                     alt="<?= htmlspecialchars($m['nama_menu']) ?>">
                                
                                <div class="card-body p-2 d-flex flex-column justify-content-between">
                                    <div>
                                        <span class="badge badge-kategori mb-2"><?= htmlspecialchars($m['nama_kategori']) ?></span>
                                        <h6 class="card-title fw-bold text-dark mb-1"><?= htmlspecialchars($m['nama_menu']) ?></h6>
                                        <p class="text-muted small mb-2">Stok: <?= $m['stok'] ?></p>
                                    </div>
                                    <div>
                                        <h6 class="text-danger fw-bold mb-3">Rp <?= number_format($m['harga'], 0, ',', '.') ?></h6>
                                        <button class="btn btn-oranye btn-sm w-100" 
                                                onclick="tambahKeKeranjang(<?= $m['id_menu'] ?>, '<?= addslashes($m['nama_menu']) ?>', <?= $m['harga'] ?>, <?= $m['stok'] ?>)">
                                            <i class="fa-solid fa-plus me-1"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Keranjang & Form Pembayaran (Kanan) -->
            <div class="col-md-5 col-lg-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold mb-3 text-secondary"><i class="fa-solid fa-cart-shopping me-2"></i>Pesanan</h5>
                    
                    <!-- Keranjang Belanja -->
                    <ul class="list-group list-group-flush mb-3" id="keranjang-list" style="max-height: 250px; overflow-y: auto;">
                        <li class="list-group-item text-center text-muted py-3">Belum ada menu yang dipilih</li>
                    </ul>

                    <hr class="my-2">

                    <!-- Ringkasan Total & Kembalian -->
                    <div class="d-flex justify-content-between align-items-center my-2">
                        <span class="fs-6 fw-bold text-secondary">Total Bayar:</span>
                        <span class="fs-4 fw-bold text-primary" id="total-harga">Rp 0</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fs-6 fw-bold text-secondary">Kembalian:</span>
                        <span class="fs-5 fw-bold text-success" id="kembalian-text">Rp 0</span>
                    </div>

                    <!-- Form Proses Bayar -->
                    <form action="proses_bayar.php" method="POST">
                        <input type="hidden" name="items" id="items-input">
                        <input type="hidden" name="total_bayar" id="total_bayar_input" value="0">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Uang Diterima (Rp)</label>
                            <input type="number" class="form-control" name="jumlah_uang" id="jumlah_uang" placeholder="0" oninput="hitungKembalian()" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Metode Pembayaran</label>
                            <select class="form-select" name="metode_pembayaran">
                                <option value="tunai">Tunai</option>
                                <option value="qris">QRIS</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-oranye w-100 py-2 fs-6">
                            <i class="fa-solid fa-print me-2"></i>Bayar & Cetak Struk
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Script Javascript Kasir -->
    <script src="../../assets/js/script.js"></script>
</body>
</html>