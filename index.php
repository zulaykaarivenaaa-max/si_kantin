<?php
session_start();
require_once 'config/koneksi.php';

// Ambil data menu
$stmt = $conn->prepare("SELECT menu.*, kategori.nama_kategori FROM menu JOIN kategori ON menu.id_kategori = kategori.id_kategori WHERE stok > 0 ORDER BY nama_menu ASC");
$stmt->execute();
$menus = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI KANTIN - Sistem Informasi Kantin</title>
    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        html { scroll-behavior: smooth; } /* Auto smooth scroll */
        section { padding: 80px 0; }
        .hero-section { background: #1a1a1a; color: #fff; padding: 100px 0; }
    </style>
</head>
<body data-bs-spy="scroll" data-bs-target="#navbar-kantin" data-bs-offset="70">

    <!-- Navbar Sticky Navigation -->
    <nav id="navbar-kantin" class="navbar navbar-expand-lg navbar-dark navbar-kantin fixed-top">
        <div class="container px-4">
            <a class="navbar-brand fw-bold fs-4" href="#beranda">
                <i class="fa-solid fa-utensils me-2"></i>SI KANTIN
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link text-white px-3" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link text-white px-3" href="#layanan">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link text-white px-3" href="#pemesanan">Pemesanan</a></li>
                    <li class="nav-item"><a class="nav-link text-white px-3" href="#kontak">Kontak</a></li>
                </ul>
                <button class="btn btn-light text-warning fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalPesanCepat">
                    <i class="fa-solid fa-bolt me-1"></i> Pesan Cepat
                </button>
            </div>
        </div>
    </nav>

    <!-- 1. SEKSI BERANDA -->
    <section id="beranda" class="hero-section mt-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold text-warning mb-3">Kelezatan Kantin dalam Genggaman</h1>
            <p class="lead text-light max-w-600 mx-auto mb-4">Sistem Pemesanan & Kasir Otomatis Kantin Terpadu. Nikmati sajian praktis, cepat, dan tanpa antre.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#pemesanan" class="btn btn-oranye btn-lg px-4">Pilih Menu</a>
                <a href="#layanan" class="btn btn-outline-light btn-lg px-4">Eksplorasi Layanan</a>
            </div>
        </div>
    </section>

    <!-- 2. SEKSI LAYANAN -->
    <section id="layanan" class="bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-warning fw-bold text-uppercase">// Tiga Pilar Layanan</h6>
                <h2 class="fw-bold">Layanan Utama Kantin</h2>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="card card-custom p-4 h-100">
                        <i class="fa-solid fa-bolt fs-1 text-warning mb-3"></i>
                        <h5 class="fw-bold">Pesan Cepat</h5>
                        <p class="text-muted small">Pesan hidangan secara mandiri dan ambil tanpa perlu menunggu lama di antrean.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom p-4 h-100">
                        <i class="fa-solid fa-utensils fs-1 text-warning mb-3"></i>
                        <h5 class="fw-bold">Dine-In / Takeaway</h5>
                        <p class="text-muted small">Fleksibilitas pilihan makan di tempat atau dibawa pulang sesuai kebutuhanmu.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom p-4 h-100">
                        <i class="fa-solid fa-qrcode fs-1 text-warning mb-3"></i>
                        <h5 class="fw-bold">Pembayaran Digital</h5>
                        <p class="text-muted small">Dukungan transaksi modern melalui QRIS, Transfer, maupun Tunai secara fleksibel.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. SEKSI PEMESANAN (KASIR / MENU) -->
    <section id="pemesanan">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-warning fw-bold text-uppercase">// Sistem Pemesanan</h6>
                <h2 class="fw-bold">Pilih Menu Favorit Anda</h2>
            </div>
            
            <div class="row g-4">
                <!-- Daftar Menu -->
                <div class="col-lg-8">
                    <div class="row row-cols-1 row-cols-md-3 g-3">
                        <?php foreach ($menus as $m): ?>
                        <?php 
                            $foto = !empty($m['gambar']) && file_exists("assets/images/" . $m['gambar']) 
                                    ? $m['gambar'] 
                                    : 'default.jpg';
                        ?>
                        <div class="col">
                            <div class="card h-100 border-light-subtle shadow-sm p-2 text-center">
                                <img src="assets/images/<?= $foto ?>" class="card-img-top img-menu" alt="<?= htmlspecialchars($m['nama_menu']) ?>">
                                <div class="card-body p-2 d-flex flex-column justify-content-between">
                                    <div>
                                        <span class="badge badge-kategori mb-2"><?= htmlspecialchars($m['nama_kategori']) ?></span>
                                        <h6 class="card-title fw-bold text-dark mb-1"><?= htmlspecialchars($m['nama_menu']) ?></h6>
                                        <p class="text-muted small mb-2">Stok: <?= $m['stok'] ?></p>
                                    </div>
                                    <div>
                                        <h6 class="text-danger fw-bold mb-3">Rp <?= number_format($m['harga'], 0, ',', '.') ?></h6>
                                        <button class="btn btn-oranye btn-sm w-100" onclick="tambahKeKeranjang(<?= $m['id_menu'] ?>, '<?= addslashes($m['nama_menu']) ?>', <?= $m['harga'] ?>, <?= $m['stok'] ?>)">
                                            <i class="fa-solid fa-plus me-1"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Panel Pesanan -->
                <div class="col-lg-4">
                    <div class="card card-custom p-4 sticky-top" style="top: 90px;">
                        <h5 class="fw-bold mb-3 text-secondary"><i class="fa-solid fa-cart-shopping me-2"></i>Ringkasan Pesanan</h5>
                        <ul class="list-group list-group-flush mb-3" id="keranjang-list" style="max-height: 200px; overflow-y: auto;">
                            <li class="list-group-item text-center text-muted py-3">Belum ada menu yang dipilih</li>
                        </ul>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center my-2">
                            <span class="fs-6 fw-bold text-secondary">Total:</span>
                            <span class="fs-4 fw-bold text-primary" id="total-harga">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-6 fw-bold text-secondary">Kembalian:</span>
                            <span class="fs-5 fw-bold text-success" id="kembalian-text">Rp 0</span>
                        </div>
                        <form action="module/kasir/proses_bayar.php" method="POST" target="_blank">
                            <input type="hidden" name="items" id="items-input">
                            <input type="hidden" name="total_bayar" id="total_bayar_input" value="0">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Nominal Uang (Rp)</label>
                                <input type="number" class="form-control" name="jumlah_uang" id="jumlah_uang" placeholder="0" oninput="hitungKembalian()" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Metode Pembayaran</label>
                                <select class="form-select" name="metode_pembayaran">
                                    <option value="tunai">Tunai</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-oranye w-100 py-2">
                                <i class="fa-solid fa-print me-2"></i>Bayar & Cetak Struk
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. SEKSI KONTAK -->
    <section id="kontak" class="bg-dark text-white">
        <div class="container text-center">
            <h6 class="text-warning fw-bold text-uppercase">// Koneksi & Lokasi</h6>
            <h2 class="fw-bold mb-4">Kunjungi Kantin Kami</h2>
            <p class="text-muted mb-4">Gedung Utama Kampus, Lantai 1. Buka Senin - Jumat pukul 07.00 - 16.00 WIB.</p>
            <a href="login.php" class="btn btn-outline-warning btn-sm"><i class="fa-solid fa-lock me-1"></i> Akses Login Admin/Kasir</a>
        </div>
    </section>

    <!-- MODAL POP-UP PESANAN CEPAT (Sama Seperti di Video Dosen) -->
    <div class="modal fade" id="modalPesanCepat" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-warning"><i class="fa-solid fa-bolt me-2"></i>Konfirmasi Pesanan Cepat</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="#pemesanan" onsubmit="alert('Pesanan cepat berhasil! Silakan tuntaskan di panel pesanan.');">
                        <div class="mb-3">
                            <label class="form-label small">NAMA PEMESAN</label>
                            <input type="text" class="form-control bg-secondary text-white border-0" placeholder="Nama Anda untuk label cup/piring" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">METODE PENGAMBILAN</label>
                            <select class="form-select bg-secondary text-white border-0">
                                <option>Pick-Up di Bar (Siap 10 Menit)</option>
                                <option>Dine-In (Antar ke Meja)</option>
                                <option>Kurir Instan Lokal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">KUSTOMISASI / CATATAN KHUSUS</label>
                            <textarea class="form-control bg-secondary text-white border-0" rows="2" placeholder="Contoh: Pedas sedang, tanpa sayur"></textarea>
                        </div>
                        <button type="submit" class="btn btn-oranye w-100" data-bs-dismiss="modal">Lanjutkan ke Pemesanan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>