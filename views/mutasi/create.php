<!-- bagian nurul khoyimah start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Buat Surat Perintah Mutasi (SPM) Baru</h1>
        <p class="page-subtitle">Formulir pemindahan fisik barang antar gudang logistik terintegrasi</p>
    </div>
    <div>
        <a href="<?= base_url('?page=mutasi') ?>" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Riwayat Mutasi</span>
        </a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert-banner alert-danger">
        <ul style="margin-left: 20px;">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card" style="max-width: 880px; margin: 0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 24px;">
        <div style="display:flex; align-items:center; gap: 10px;">
            <span style="font-weight: 700; color: var(--primary); font-size: 16px;">Formulir SPM Terbit</span>
            <span class="badge badge-blue">Draft Approval</span>
        </div>
        <div>
            <span class="text-muted" style="font-size: 12px;">NO SPM SISTEM:</span>
            <code class="code-mono" style="font-size:13px; font-weight:700; color:var(--primary);"><?= e($old['no_mutasi']) ?></code>
        </div>
    </div>

    <form action="<?= base_url('?page=mutasi&action=create') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="no_mutasi" value="<?= e($old['no_mutasi']) ?>">

        <div class="form-grid-2">
            <!-- Pilihan Batch Asal (Urut FEFO) -->
            <div class="form-group">
                <label for="mutasiSelectBatch" class="form-label">Produk &amp; Batch Stok Asal (FEFO) <span class="text-danger">*</span></label>
                <select id="mutasiSelectBatch" name="id_batch" class="form-select" required>
                    <option value="">-- Pilih Batch Stok Asal --</option>
                    <?php foreach ($batches as $b): ?>
                        <option value="<?= $b['id'] ?>"
                                data-stok="<?= (int)$b['stok_tersedia'] ?>"
                                data-depo-id="<?= $b['id_depo'] ?>"
                                data-depo-nama="<?= e($b['nama_depo']) ?>"
                                data-depo-total="18400"
                                <?= ($old['id_batch'] == $b['id']) ? 'selected' : '' ?>>
                            <?= e($b['nama_produk']) ?> (Batch: <?= e($b['no_batch']) ?>) &bull; <?= e($b['kode_depo']) ?> &bull; Sisa: <?= format_number($b['stok_tersedia']) ?> CTN &bull; Exp: <?= format_date_id($b['tgl_expired']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-help">Pilihan diurutkan berdasarkan kedaluwarsa terdekat (FEFO Aktif).</div>
            </div>

            <!-- Depo Asal (Otomatis) -->
            <div class="form-group">
                <label class="form-label">Depo Gudang Asal Pengirim</label>
                <input type="text" id="mutasiDepoAsalAuto" class="form-input" style="background-color: #F8FAFC;" readonly value="Pilih batch untuk memuat depo asal">
                <div class="form-help">Terisi otomatis sesuai lokasi fisik batch persediaan.</div>
            </div>
        </div>

        <div class="form-grid-2">
            <!-- Depo Tujuan -->
            <div class="form-group">
                <label for="mutasiSelectTujuan" class="form-label">Depo Gudang Tujuan Penerima <span class="text-danger">*</span></label>
                <select id="mutasiSelectTujuan" name="id_depo_tujuan" class="form-select" required>
                    <option value="">-- Pilih Depo Penerima --</option>
                    <?php foreach ($depos as $d): ?>
                        <option value="<?= $d['id'] ?>" data-stok="2500" <?= ($old['id_depo_tujuan'] == $d['id']) ? 'selected' : '' ?>>
                            <?= e($d['kode_depo']) ?> - <?= e($d['nama_depo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tanggal Pemindahan -->
            <div class="form-group">
                <label for="mutasiTanggal" class="form-label">Tanggal Pelaksanaan Mutasi <span class="text-danger">*</span></label>
                <input type="date" id="mutasiTanggal" name="tanggal" class="form-input" value="<?= e($old['tanggal']) ?>" required>
            </div>
        </div>

        <!-- Jumlah Transfer Karton dengan Tombol Maks -->
        <div class="form-group">
            <label for="mutasiJumlah" class="form-label">Jumlah Transfer (Karton) <span class="text-danger">*</span></label>
            <div style="display:flex; gap: 8px;">
                <input type="number" id="mutasiJumlah" name="jumlah" class="form-input" min="1" placeholder="Masukkan jumlah karton..." value="<?= e($old['jumlah']) ?>" required>
                <button type="button" id="btnMaxQty" class="btn btn-secondary" style="flex-shrink:0;">Maks</button>
            </div>
            <div class="form-help">Jumlah karton tidak boleh melebihi sisa fisik batch asal.</div>
        </div>

        <!-- LIVE STOCK SIMULATOR Sesuai Prototype -->
        <div class="stock-simulator-wrap">
            <div class="simulator-header">
                <div class="simulator-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>LIVE STOCK SIMULATOR: DAMPAK PEMINDAHAN TERHADAP BUFFER KUOTA</span>
                </div>
                <span class="simulator-badge">Kalkulasi Otomatis Real-time</span>
            </div>

            <div class="simulator-grid">
                <!-- Depo Asal Pasca Mutasi -->
                <div class="simulator-card">
                    <div class="sim-label" id="simAsalNama">DEPO ASAL PASCA MUTASI</div>
                    <div class="sim-qty" id="simAsalQty">- Karton</div>
                    <div>
                        <span id="simAsalStatus" class="sim-status badge badge-green">Stok Aman</span>
                    </div>
                    <div style="margin-top: 10px; font-size: 11px; color:#64748B;">
                        Batas Minimum Aman Terjaga &bull; Sistem FEFO Terkendali
                    </div>
                </div>

                <!-- Depo Tujuan Pasca Mutasi -->
                <div class="simulator-card">
                    <div class="sim-label" id="simTujuanNama">DEPO TUJUAN PASCA MUTASI</div>
                    <div class="sim-qty" id="simTujuanQty">- Karton</div>
                    <div>
                        <span id="simTujuanStatus" class="sim-status badge badge-green">Memenuhi Buffer Kuota</span>
                    </div>
                    <div style="margin-top: 10px; font-size: 11px; color:#64748B;">
                        Persediaan Bertambah &bull; Penyeimbangan Multi-Depo
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="<?= base_url('?page=mutasi') ?>" class="btn btn-secondary">Batal &amp; Bersihkan</a>
            <button type="submit" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span>Simpan &amp; Terbitkan Surat Jalan</span>
            </button>
        </div>
    </form>
</div>
<!-- bagian nurul khoyimah end -->

