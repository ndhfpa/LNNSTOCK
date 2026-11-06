// bagian nadhifatul hilmy ns start

document.addEventListener('DOMContentLoaded', function () {
    // 1. Live Search Tabel Master Depo
    const searchDepo = document.getElementById('searchDepo');
    if (searchDepo) {
        searchDepo.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#tableDepo tbody tr');

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // 2. Filter Tab Zona pada Denah Rak (Semua / Zona A / B / C)
    const zoneTabs = document.querySelectorAll('.zone-tab-btn');
    const rackCards = document.querySelectorAll('.rack-card');

    if (zoneTabs.length > 0 && rackCards.length > 0) {
        zoneTabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                zoneTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const selectedZone = this.getAttribute('data-zone');

                rackCards.forEach(function (card) {
                    const cardZone = card.getAttribute('data-zone');
                    if (selectedZone === 'Semua' || cardZone === selectedZone) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }

    // 3. Modal Detail Layout Rak saat kartu diklik
    const modalDetailRak = document.getElementById('modalDetailRak');
    if (modalDetailRak && rackCards.length > 0) {
        rackCards.forEach(function (card) {
            card.addEventListener('click', function () {
                const rakId = this.getAttribute('data-id');
                const kodeRak = this.getAttribute('data-kode');
                const zona = this.getAttribute('data-zona');
                const kapasitas = this.getAttribute('data-kapasitas');
                const terisi = this.getAttribute('data-terisi');
                const persen = this.getAttribute('data-persen');
                const status = this.getAttribute('data-status');
                const batchesJson = this.getAttribute('data-batches');

                // Isi data ke modal
                document.getElementById('detailRakKode').textContent = kodeRak;
                document.getElementById('detailRakZona').textContent = 'Zona ' + zona;
                document.getElementById('detailRakKapasitas').textContent = kapasitas + ' Karton';
                document.getElementById('detailRakTerisi').textContent = terisi + ' Karton';
                document.getElementById('detailRakPersen').textContent = persen + '%';
                
                const btnEdit = document.getElementById('detailRakBtnEdit');
                if (btnEdit) {
                    btnEdit.href = btnEdit.getAttribute('data-base') + rakId;
                }

                const btnDelete = document.getElementById('detailRakBtnDelete');
                if (btnDelete) {
                    btnDelete.setAttribute('onclick', "confirmDelete('" + btnDelete.getAttribute('data-base') + rakId + "', 'Rak " + kodeRak + "')");
                }

                // Render tabel batch tersimpan di rak
                const tbody = document.getElementById('detailRakBatchList');
                tbody.innerHTML = '';

                try {
                    const batches = JSON.parse(batchesJson || '[]');
                    if (batches.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted" style="padding:16px;">Belum ada persediaan barang di rak ini.</td></tr>';
                    } else {
                        batches.forEach(function (b) {
                            let badgeClass = 'badge-green';
                            let labelSisa = b.sisa_hari + ' hari lagi';
                            if (b.sisa_hari < 0) {
                                badgeClass = 'badge-red';
                                labelSisa = 'Sudah Kedaluwarsa';
                            } else if (b.sisa_hari <= 30) {
                                badgeClass = 'badge-red';
                            } else if (b.sisa_hari <= 90) {
                                badgeClass = 'badge-yellow';
                            }

                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>${b.nama_produk}</strong><br><small class="text-muted">${b.kode_sku}</small></td>
                                <td><code>${b.no_batch}</code></td>
                                <td><strong>${b.jumlah}</strong> Karton</td>
                                <td><span class="badge ${badgeClass}">${labelSisa}</span></td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                } catch (e) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-danger">Gagal memuat batch.</td></tr>';
                }

                modalDetailRak.classList.add('show');
            });
        });
    }
});

function closeDetailRakModal() {
    const modal = document.getElementById('modalDetailRak');
    if (modal) {
        modal.classList.remove('show');
    }
}

// bagian nadhifatul hilmy ns end

