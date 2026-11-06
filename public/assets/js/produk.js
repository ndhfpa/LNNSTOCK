// bagian lutfi firmansyah start

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchProduk');
    const filterKategori = document.getElementById('filterKategori');
    const filterStatus = document.getElementById('filterStatus');
    const btnResetFilter = document.getElementById('btnResetFilter');
    const rows = document.querySelectorAll('#tableProduk tbody tr');
    const countDisplay = document.getElementById('displayCountProduk');
    const totalCount = rows.length;

    // 1. Live Filter Kombinasi (Search + Kategori + Status Stok)
    function applyFilter() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const cat = filterKategori ? filterKategori.value : '';
        const stat = filterStatus ? filterStatus.value : '';

        let visibleCount = 0;

        rows.forEach(function (row) {
            const sku = row.getAttribute('data-sku') || '';
            const nama = row.getAttribute('data-nama') || '';
            const rowCat = row.getAttribute('data-kategori') || '';
            const rowStat = row.getAttribute('data-status') || '';

            const matchSearch = query === '' || sku.toLowerCase().includes(query) || nama.toLowerCase().includes(query);
            const matchCat = cat === '' || rowCat === cat;
            const matchStat = stat === '' || rowStat === stat;

            if (matchSearch && matchCat && matchStat) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (countDisplay) {
            countDisplay.textContent = visibleCount;
        }
    }

    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (filterKategori) filterKategori.addEventListener('change', applyFilter);
    if (filterStatus) filterStatus.addEventListener('change', applyFilter);

    if (btnResetFilter) {
        btnResetFilter.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (filterKategori) filterKategori.value = '';
            if (filterStatus) filterStatus.value = '';
            applyFilter();
        });
    }

    // 2. Banner Peringatan Stok Kritis Otomatis
    const alertContainer = document.getElementById('bannerKritisContainer');
    if (alertContainer) {
        const criticalItems = [];
        rows.forEach(function (row) {
            if (row.getAttribute('data-status') === 'Kritis') {
                criticalItems.push(row.getAttribute('data-nama'));
            }
        });

        if (criticalItems.length > 0) {
            const banner = document.createElement('div');
            banner.className = 'banner-kritis-produk';
            banner.innerHTML = `
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div style="flex:1;">
                    <strong>Peringatan Buffer Stok Kritis!</strong><br>
                    Stok <strong>${criticalItems.join(', ')}</strong> berada di bawah batas minimum buffer. Segera lakukan pemeriksaan dan pengadaan persediaan.
                </div>
            `;
            alertContainer.appendChild(banner);
        }
    }

    // 3. Image Upload Preview
    const inputGambar = document.getElementById('inputGambarProduk');
    const previewImg = document.getElementById('previewGambarProduk');
    if (inputGambar && previewImg) {
        inputGambar.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file foto maksimal adalah 2 MB.');
                    this.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// bagian lutfi firmansyah end

