<!-- bagian lutfi firmansyah start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Master Produk</h1>
        <p class="page-subtitle">Perbarui spesifikasi SKU: <?= e($produk['nama_produk']) ?></p>
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
    <form action="<?= base_url('?page=produk&action=edit&id=' . $produk['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="kode_sku" class="form-label">Kode SKU <span class="text-danger">*</span></label>
                <input type="text" id="kode_sku" name="kode_sku" class="form-input" value="<?= e($old['kode_sku']) ?>" required>
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
            <input type="text" id="nama_produk" name="nama_produk" class="form-input" value="<?= e($old['nama_produk']) ?>" required>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="isi_per_karton" class="form-label">Isi per Karton (Pcs) <span class="text-danger">*</span></label>
                <input type="number" id="isi_per_karton" name="isi_per_karton" class="form-input" min="1" value="<?= e($old['isi_per_karton']) ?>" required>
            </div>

            <div class="form-group">
                <label for="masa_simpan_bulan" class="form-label">Masa Simpan (Bulan) <span class="text-danger">*</span></label>
                <input type="number" id="masa_simpan_bulan" name="masa_simpan_bulan" class="form-input" min="1" value="<?= e($old['masa_simpan_bulan']) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="buffer_minimum" class="form-label">Batas Buffer Minimum (Karton) <span class="text-danger">*</span></label>
            <input type="number" id="buffer_minimum" name="buffer_minimum" class="form-input" min="0" value="<?= e($old['buffer_minimum']) ?>" required>
        </div>

        <div class="form-group">
            <label for="inputGambarProduk" class="form-label">Ganti Foto / Thumbnail Produk</label>
            <input type="file" id="inputGambarProduk" name="gambar" class="form-input" accept="image/jpeg,image/png,image/webp">
            <div class="form-help">Biarkan kosong jika tidak ingin mengubah foto produk.</div>

            <div class="upload-preview-wrap">
                <div class="upload-preview-box">
                    <?php if (!empty($produk['gambar']) && file_exists(__DIR__ . '/../../public/uploads/produk/' . $produk['gambar'])): ?>
                        <img id="previewGambarProduk" src="<?= base_url('uploads/produk/' . $produk['gambar']) ?>" alt="<?= e($produk['nama_produk']) ?>">
                    <?php else: ?>
                        <img id="previewGambarProduk" src="" alt="Pratinjau" style="display:none;">
                        <span class="text-muted" style="font-size:11px;">Belum Ada Foto</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px;">
            <a href="<?= base_url('?page=produk') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
<!-- bagian lutfi firmansyah end -->

