// bagian nurul khoyimah start

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchBatch');
    const filterDepo = document.getElementById('filterDepo');
    const filterStatus = document.getElementById('filterStatusBatch');
    const rows = document.querySelectorAll('#tableBatch tbody tr');

    // 1. Live Filter Tabel Monitoring Kedaluwarsa
    function applyBatchFilter() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const depoId = filterDepo ? filterDepo.value : '';
        const status = filterStatus ? filterStatus.value : '';

        rows.forEach(function (row) {
            const batchNo = (row.getAttribute('data-batch') || '').toLowerCase();
            const produk = (row.getAttribute('data-produk') || '').toLowerCase();
            const rowDepo = row.getAttribute('data-depo') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchSearch = query === '' || batchNo.includes(query) || produk.includes(query);
            const matchDepo = depoId === '' || rowDepo === depoId;
            const matchStatus = status === '' || rowStatus === status;

            if (matchSearch && matchDepo && matchStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', applyBatchFilter);
    if (filterDepo) filterDepo.addEventListener('change', applyBatchFilter);
    if (filterStatus) filterStatus.addEventListener('change', applyBatchFilter);

    // 2. Alert Banner JS Membaca data-expired tiap baris
    const alertBannerContainer = document.getElementById('alertKedaluwarsaContainer');
    if (alertBannerContainer && rows.length > 0) {
        const urgentList = [];
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        rows.forEach(function (row) {
            const expDateStr = row.getAttribute('data-expired');
            const produkName = row.getAttribute('data-produk-name');

            if (expDateStr) {
                const expDate = new Date(expDateStr);
                expDate.setHours(0, 0, 0, 0);

                const diffTime = expDate - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays < 0) {
                    urgentList.push(`${produkName} — <em>Sudah Kedaluwarsa</em>`);
                } else if (diffDays <= 30) {
                    urgentList.push(`${produkName} — <strong>${diffDays} hari lagi</strong>`);
                }
            }
        });

        if (urgentList.length > 0) {
            const alertBox = document.createElement('div');
            alertBox.className = 'alert-banner alert-danger';
            alertBox.innerHTML = `
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <div style="flex:1;">
                    <strong>Peringatan FEFO! Terdapat ${urgentList.length} batch yang mendekati / telah lewat batas kedaluwarsa:</strong><br>
                    ${urgentList.slice(0, 3).join(' &bull; ')} ${urgentList.length > 3 ? `(+${urgentList.length - 3} batch lainnya)` : ''}
                </div>
            `;
            alertBannerContainer.appendChild(alertBox);
        }
    }

    // 3. Form Batch: Auto Calculate Expired Date dari Produk & Tanggal Masuk
    const selectProduk = document.getElementById('batchSelectProduk');
    const inputTglMasuk = document.getElementById('batchTglMasuk');
    const inputTglExpired = document.getElementById('batchTglExpired');

    function calculateExpired() {
        if (!selectProduk || !inputTglMasuk || !inputTglExpired) return;
        const selectedOpt = selectProduk.options[selectProduk.selectedIndex];
        if (!selectedOpt) return;

        const masaSimpanBulan = parseInt(selectedOpt.getAttribute('data-masa-simpan') || '12', 10);
        const tglMasukVal = inputTglMasuk.value;

        if (tglMasukVal) {
            const masuk = new Date(tglMasukVal);
            if (!isNaN(masuk.getTime())) {
                masuk.setMonth(masuk.getMonth() + masaSimpanBulan);
                const yyyy = masuk.getFullYear();
                const mm = String(masuk.getMonth() + 1).padStart(2, '0');
                const dd = String(masuk.getDate()).padStart(2, '0');
                inputTglExpired.value = `${yyyy}-${mm}-${dd}`;
            }
        }
    }

    if (selectProduk) selectProduk.addEventListener('change', calculateExpired);
    if (inputTglMasuk) inputTglMasuk.addEventListener('change', calculateExpired);

    // 4. Form Batch: Filter Pilihan Rak Berdasarkan Depo yang Dipilih
    const selectDepo = document.getElementById('batchSelectDepo');
    const selectRak = document.getElementById('batchSelectRak');

    if (selectDepo && selectRak) {
        selectDepo.addEventListener('change', function () {
            const selectedDepoId = this.value;
            const options = selectRak.querySelectorAll('option');

            options.forEach(function (opt) {
                const optDepoId = opt.getAttribute('data-depo');
                if (!optDepoId || optDepoId === '' || optDepoId === selectedDepoId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });

            // Reset seleksi jika pilihan sebelumnya tidak cocok
            const currentSelected = selectRak.options[selectRak.selectedIndex];
            if (currentSelected && currentSelected.getAttribute('data-depo') && currentSelected.getAttribute('data-depo') !== selectedDepoId) {
                selectRak.value = '';
            }
        });
    }
});

// bagian nurul khoyimah end

