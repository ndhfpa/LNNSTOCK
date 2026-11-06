// bagian nurul khoyimah start

document.addEventListener('DOMContentLoaded', function () {
    // 1. Live Filter Riwayat Mutasi (Tab Status + Input Search)
    const statusTabs = document.querySelectorAll('.status-tab-btn');
    const searchInput = document.getElementById('searchMutasi');
    const rows = document.querySelectorAll('#tableMutasi tbody tr');

    let currentStatus = 'Semua';

    function applyMutasiFilter() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

        rows.forEach(function (row) {
            const noMutasi = (row.getAttribute('data-nomutasi') || '').toLowerCase();
            const produk = (row.getAttribute('data-produk') || '').toLowerCase();
            const rute = (row.getAttribute('data-rute') || '').toLowerCase();
            const status = row.getAttribute('data-status') || '';

            const matchSearch = query === '' || noMutasi.includes(query) || produk.includes(query) || rute.includes(query);
            const matchStatus = currentStatus === 'Semua' || status === currentStatus;

            if (matchSearch && matchStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (statusTabs.length > 0) {
        statusTabs.forEach(function (tab) {
            tab.addEventListener('click', function (e) {
                e.preventDefault();
                statusTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentStatus = this.getAttribute('data-status');
                applyMutasiFilter();
            });
        });
    }

    if (searchInput) searchInput.addEventListener('input', applyMutasiFilter);

    // 2. Form Mutasi: Live Stock Simulator Real-Time
    const selectBatch = document.getElementById('mutasiSelectBatch');
    const selectTujuan = document.getElementById('mutasiSelectTujuan');
    const inputJumlah = document.getElementById('mutasiJumlah');
    const btnMax = document.getElementById('btnMaxQty');

    const simAsalNama = document.getElementById('simAsalNama');
    const simAsalQty = document.getElementById('simAsalQty');
    const simAsalStatus = document.getElementById('simAsalStatus');

    const simTujuanNama = document.getElementById('simTujuanNama');
    const simTujuanQty = document.getElementById('simTujuanQty');
    const simTujuanStatus = document.getElementById('simTujuanStatus');

    function updateSimulator() {
        if (!selectBatch || !inputJumlah) return;

        const selectedBatchOpt = selectBatch.options[selectBatch.selectedIndex];
        const selectedTujuanOpt = selectTujuan ? selectTujuan.options[selectTujuan.selectedIndex] : null;

        if (!selectedBatchOpt || !selectedBatchOpt.value) {
            return;
        }

        const maxStok = parseInt(selectedBatchOpt.getAttribute('data-stok') || '0', 10);
        const depoAsalNama = selectedBatchOpt.getAttribute('data-depo-nama') || 'Depo Asal';
        const depoAsalTotal = parseInt(selectedBatchOpt.getAttribute('data-depo-total') || '1000', 10);

        let qty = parseInt(inputJumlah.value || '0', 10);
        if (isNaN(qty) || qty < 0) qty = 0;

        // Cegah input melebihi kapasitas stok batch
        if (qty > maxStok) {
            qty = maxStok;
            inputJumlah.value = maxStok;
        }

        // Hitung Depo Asal Pasca Mutasi
        const sisaAsal = Math.max(0, depoAsalTotal - qty);
        if (simAsalNama) simAsalNama.textContent = depoAsalNama;
        if (simAsalQty) simAsalQty.textContent = new Intl.NumberFormat('id-ID').format(sisaAsal) + ' Karton';

        if (simAsalStatus) {
            if (sisaAsal <= 100) {
                simAsalStatus.className = 'sim-status badge badge-red';
                simAsalStatus.textContent = 'Mendekati Batas Kritis';
            } else if (sisaAsal <= 300) {
                simAsalStatus.className = 'sim-status badge badge-yellow';
                simAsalStatus.textContent = 'Menipis';
            } else {
                simAsalStatus.className = 'sim-status badge badge-green';
                simAsalStatus.textContent = 'Stok Sangat Aman';
            }
        }

        // Hitung Depo Tujuan Pasca Mutasi
        if (selectedTujuanOpt && selectedTujuanOpt.value) {
            const depoTujuanNama = selectedTujuanOpt.textContent.trim();
            const depoTujuanAwal = parseInt(selectedTujuanOpt.getAttribute('data-stok') || '500', 10);
            const totalTujuan = depoTujuanAwal + qty;

            if (simTujuanNama) simTujuanNama.textContent = depoTujuanNama;
            if (simTujuanQty) simTujuanQty.textContent = new Intl.NumberFormat('id-ID').format(totalTujuan) + ' Karton';

            if (simTujuanStatus) {
                simTujuanStatus.className = 'sim-status badge badge-green';
                simTujuanStatus.textContent = `Memenuhi Buffer Kuota (+${qty})`;
            }
        }
    }

    if (selectBatch) {
        selectBatch.addEventListener('change', function () {
            const selectedOpt = this.options[this.selectedIndex];
            if (selectedOpt && selectedOpt.value) {
                const maxStok = selectedOpt.getAttribute('data-stok');
                inputJumlah.max = maxStok;
                inputJumlah.placeholder = `Maks: ${maxStok}`;

                // Set text input depo asal yang terisi otomatis
                const inputAsalAuto = document.getElementById('mutasiDepoAsalAuto');
                if (inputAsalAuto) {
                    inputAsalAuto.value = selectedOpt.getAttribute('data-depo-nama');
                }

                // Sembunyikan depo asal dari opsi depo tujuan
                const asalDepoId = selectedOpt.getAttribute('data-depo-id');
                if (selectTujuan) {
                    const tujuanOpts = selectTujuan.querySelectorAll('option');
                    tujuanOpts.forEach(function (opt) {
                        if (opt.value === asalDepoId) {
                            opt.disabled = true;
                            opt.style.display = 'none';
                        } else {
                            opt.disabled = false;
                            opt.style.display = '';
                        }
                    });
                    if (selectTujuan.value === asalDepoId) {
                        selectTujuan.value = '';
                    }
                }
            }
            updateSimulator();
        });
    }

    if (selectTujuan) selectTujuan.addEventListener('change', updateSimulator);
    if (inputJumlah) inputJumlah.addEventListener('input', updateSimulator);

    if (btnMax && selectBatch && inputJumlah) {
        btnMax.addEventListener('click', function () {
            const selectedOpt = selectBatch.options[selectBatch.selectedIndex];
            if (selectedOpt && selectedOpt.value) {
                const maxStok = selectedOpt.getAttribute('data-stok');
                inputJumlah.value = maxStok;
                updateSimulator();
            }
        });
    }

    // Jalankan awal jika form sudah terisi (misal alokasi dari batch kedaluwarsa)
    if (selectBatch && selectBatch.value) {
        selectBatch.dispatchEvent(new Event('change'));
    }
});

// bagian nurul khoyimah end

