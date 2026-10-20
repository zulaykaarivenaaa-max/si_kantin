// Variabel global untuk menyimpan item keranjang
let keranjang = [];

// Fungsi menambah item ke keranjang
function tambahKeKeranjang(id, nama, harga, stok) {
    let item = keranjang.find(i => i.id === id);
    
    if (item) {
        if (item.jumlah < stok) {
            item.jumlah++;
        } else {
            alert('Stok menu ini sudah maksimal!');
            return;
        }
    } else {
        keranjang.push({
            id: id,
            nama: nama,
            harga: harga,
            jumlah: 1,
            stok: stok
        });
    }
    updateKeranjangView();
}

// Fungsi mengubah jumlah kuantitas (+ / -)
function ubahJumlah(id, delta) {
    let item = keranjang.find(i => i.id === id);
    if (item) {
        item.jumlah += delta;
        if (item.jumlah <= 0) {
            keranjang = keranjang.filter(i => i.id !== id);
        } else if (item.jumlah > item.stok) {
            alert('Jumlah melebihi stok yang tersedia!');
            item.jumlah = item.stok;
        }
    }
    updateKeranjangView();
}

// Memperbarui tampilan daftar keranjang & total harga
function updateKeranjangView() {
    const listContainer = document.getElementById('keranjang-list');
    const inputItems = document.getElementById('items-input');
    const totalElement = document.getElementById('total-harga');
    
    if (!listContainer) return;
    
    listContainer.innerHTML = '';
    let total = 0;

    if (keranjang.length === 0) {
        listContainer.innerHTML = '<li class="list-group-item text-center text-muted py-3">Belum ada menu yang dipilih</li>';
    } else {
        keranjang.forEach(item => {
            let subtotal = item.harga * item.jumlah;
            total += subtotal;
            
            listContainer.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <h6 class="my-0 fw-semibold">${item.nama}</h6>
                        <small class="text-muted">Rp ${item.harga.toLocaleString('id-ID')} x ${item.jumlah}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="ubahJumlah(${item.id}, -1)">-</button>
                        <span class="fw-bold">${item.jumlah}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="ubahJumlah(${item.id}, 1)">+</button>
                        <span class="fw-bold ms-2">Rp ${subtotal.toLocaleString('id-ID')}</span>
                    </div>
                </li>
            `;
        });
    }

    totalElement.innerText = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('total_bayar_input').value = total;
    inputItems.value = JSON.stringify(keranjang);
    hitungKembalian();
}

// Perhitungan kembalian uang tunai secara real-time
function hitungKembalian() {
    let total = parseFloat(document.getElementById('total_bayar_input').value) || 0;
    let bayar = parseFloat(document.getElementById('jumlah_uang').value) || 0;
    let kembalian = bayar - total;
    
    let elKembalian = document.getElementById('kembalian-text');
    if (!elKembalian) return;

    if (kembalian < 0) {
        elKembalian.innerText = 'Uang Kurang!';
        elKembalian.className = 'fs-5 fw-bold text-danger';
    } else {
        elKembalian.innerText = 'Rp ' + kembalian.toLocaleString('id-ID');
        elKembalian.className = 'fs-5 fw-bold text-success';
    }
}

// Menangani submit form tambah menu tanpa pindah halaman (AJAX)
document.addEventListener('DOMContentLoaded', function() {
    const formTambah = document.getElementById('form-tambah-menu');
    if (formTambah) {
        formTambah.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('module/menu/tambah.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert('Menu berhasil ditambahkan!');
                location.reload(); // Refresh halaman agar menu baru langsung muncul
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menambahkan menu.');
            });
        });
    }
});