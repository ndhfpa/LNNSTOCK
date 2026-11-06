<!-- bagian lutfi firmansyah start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Produk Baru</h1>
        <p class="page-subtitle">Daftarkan SKU produk FMCG baru pada lini produksi PT Riyana Cipta Pangan Indonesia</p>
    </div>
    <div>
        <a href="<?= base_url('?page=produk') ?>" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Katalog</span>
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
    <form action="<?= base_url('?page=produk&action=create') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="kode_sku" class="form-label">Kode SKU <span class="text-danger">*</span></label>
                <input type="text" id="kode_sku" name="kode_sku" class="form-input" placeholder="Cth: HJ-STR-150" value="<?= e($old['kode_sku']) ?>" required autofocus>
                <div class="form-help">Pengenal unik SKU (cth: HJ-STR-150).</div>
            </div>

            <div class="form-group">
                <label for="kategori" class="form-label">Kategori Produk <span class="text-danger">*</span></label>
                <select id="kategori" name="kategori" class="form-select" required>
                    <option value="Jelly Cups" <?= ($old['kategori'] === 'Jelly Cups') ? 'selected' : '' ?>>Jelly Cups</option>
                    <option value="Wafer Roll" <?= ($old['kategori'] === 'Wafer Roll') ? 'selected' : '' ?>>Wafer Roll</option>
                    <option value="Biscuits & Cookies" <?= ($old['kategori'] === 'Biscuits & Cookies') ? 'selected' : '' ?>>Biscuits &amp; Cookies</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="nama_produk" class="form-label">Nama Produk Lengkap <span class="text-danger">*</span></label>
            <input type="text" id="nama_produk" name="nama_produk" class="form-input" placeholder="Cth: Happy Jelly Strawberry 150g" value="<?= e($old['nama_produk']) ?>" required>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="isi_per_karton" class="form-label">Isi per Karton (Pcs) <span class="text-danger">*</span></label>
                <input type="number" id="isi_per_karton" name="isi_per_karton" class="form-input" min="1" value="<?= e($old['isi_per_karton']) ?>" required>
                <div class="form-help">Konversi satuan karton ke kemasan eceran.</div>
            </div>

            <div class="form-group">
                <label for="masa_simpan_bulan" class="form-label">Masa Simpan (Bulan) <span class="text-danger">*</span></label>
                <input type="number" id="masa_simpan_bulan" name="masa_simpan_bulan" class="form-input" min="1" value="<?= e($old['masa_simpan_bulan']) ?>" required>
                <div class="form-help">Dasar kalkulasi tanggal kedaluwarsa FEFO.</div>
            </div>
        </div>

        <div class="form-group">
            <label for="buffer_minimum" class="form-label">Batas Buffer Minimum (Karton) <span class="text-danger">*</span></label>
            <input type="number" id="buffer_minimum" name="buffer_minimum" class="form-input" min="0" value="<?= e($old['buffer_minimum']) ?>" required>
            <div class="form-help">Ambang batas peringatan stok menipis / kritis seluruh depo.</div>
        </div>

        <div class="form-group">
            <label for="inputGambarProduk" class="form-label">Foto / Thumbnail Produk</label>
            <input type="file" id="inputGambarProduk" name="gambar" class="form-input" accept="image/jpeg,image/png,image/webp">
            <div class="form-help">Format: JPG, PNG, WEBP. Maksimal 2 MB.</div>

            <div class="upload-preview-wrap">
                <div class="upload-preview-box">
                    <img id="previewGambarProduk" src="" alt="Pratinjau" style="display:none;">
                    <span class="text-muted" style="font-size:11px;">Pratinjau</span>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px;">
            <a href="<?= base_url('?page=produk') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </div>
    </form>
</div>
<!-- bagian lutfi firmansyah end -->

