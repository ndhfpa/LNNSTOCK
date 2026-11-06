<!-- bagian nurul khoyimah start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Batch Stok Baru</h1>
        <p class="page-subtitle">Registrasikan lot produksi baru masuk ke gudang dengan kalkulasi otomatis FEFO</p>
    </div>
    <div>
        <a href="<?= base_url('?page=kedaluwarsa') ?>" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Monitoring</span>
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

<div class="card" style="max-width: 760px; margin: 0 auto;">
    <form action="<?= base_url('?page=kedaluwarsa&action=create') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="batchSelectProduk" class="form-label">Produk FMCG <span class="text-danger">*</span></label>
                <select id="batchSelectProduk" name="id_produk" class="form-select" required>
                    <?php foreach ($produks as $p): ?>
                        <option value="<?= $p['id'] ?>" data-masa-simpan="<?= (int)$p['masa_simpan_bulan'] ?>" <?= ($old['id_produk'] == $p['id']) ? 'selected' : '' ?>>
                            <?= e($p['nama_produk']) ?> (<?= e($p['kode_sku']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="batchSelectDepo" class="form-label">Depo Gudang Masuk <span class="text-danger">*</span></label>
                <select id="batchSelectDepo" name="id_depo" class="form-select" required>
                    <?php foreach ($depos as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($old['id_depo'] == $d['id']) ? 'selected' : '' ?>>
                            <?= e($d['kode_depo']) ?> - <?= e($d['nama_depo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="no_batch" class="form-label">Nomor Batch Lot <span class="text-danger">*</span></label>
                <input type="text" id="no_batch" name="no_batch" class="form-input" placeholder="Cth: BATCH-2026-HJ09" value="<?= e($old['no_batch']) ?>" required>
                <div class="form-help">Nomor identifikasi unik lot produksi.</div>
            </div>

            <div class="form-group">
                <label for="batchSelectRak" class="form-label">Penempatan Rak (Opsional)</label>
                <select id="batchSelectRak" name="id_rak" class="form-select">
                    <option value="">-- Belum Masuk Rak (Transit/Staging) --</option>
                    <?php foreach ($allRaks as $r): ?>
                        <option value="<?= $r['id'] ?>" data-depo="<?= $r['id_depo'] ?>" <?= ($old['id_rak'] == $r['id']) ? 'selected' : '' ?>>
                            Rak <?= e($r['kode_rak']) ?> (Zona <?= e($r['zona']) ?> - Kap: <?= $r['kapasitas'] ?> CTN)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-help">Daftar rak otomatis tersaring sesuai depo terpilih.</div>
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="batchTglMasuk" class="form-label">Tanggal Masuk Barang (FIFO) <span class="text-danger">*</span></label>
                <input type="date" id="batchTglMasuk" name="tgl_masuk" class="form-input" value="<?= e($old['tgl_masuk']) ?>" required>
            </div>

            <div class="form-group">
                <label for="batchTglExpired" class="form-label">Tanggal Kedaluwarsa (FEFO) <span class="text-danger">*</span></label>
                <input type="date" id="batchTglExpired" name="tgl_expired" class="form-input" value="<?= e($old['tgl_expired']) ?>" required>
                <div class="form-help">Terisi otomatis sesuai masa simpan produk, dapat disesuaikan.</div>
            </div>
        </div>

        <div class="form-group">
            <label for="jumlah" class="form-label">Jumlah Persediaan Masuk (Karton) <span class="text-danger">*</span></label>
            <input type="number" id="jumlah" name="jumlah" class="form-input" min="1" value="<?= e($old['jumlah']) ?>" required>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px;">
            <a href="<?= base_url('?page=kedaluwarsa') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Batch Persediaan</button>
        </div>
    </form>
</div>
<!-- bagian nurul khoyimah end -->

