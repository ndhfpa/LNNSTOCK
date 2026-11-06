<!-- bagian nadhifatul hilmy ns start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Depo Gudang</h1>
        <p class="page-subtitle">Perbarui data fasilitas: <?= e($depo['nama_depo']) ?></p>
    </div>
    <div>
        <a href="<?= base_url('?page=depo') ?>" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Master Depo</span>
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

<div class="card" style="max-width: 720px; margin: 0 auto;">
    <form action="<?= base_url('?page=depo&action=edit&id=' . $depo['id']) ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="kode_depo" class="form-label">Kode Depo <span class="text-danger">*</span></label>
                <input type="text" id="kode_depo" name="kode_depo" class="form-input" value="<?= e($old['kode_depo']) ?>" required>
            </div>

            <div class="form-group">
                <label for="jenis_depo" class="form-label">Jenis Depo <span class="text-danger">*</span></label>
                <select id="jenis_depo" name="jenis_depo" class="form-select" required>
                    <option value="Produk Jadi" <?= ($old['jenis_depo'] === 'Produk Jadi') ? 'selected' : '' ?>>Produk Jadi</option>
                    <option value="Bahan Baku" <?= ($old['jenis_depo'] === 'Bahan Baku') ? 'selected' : '' ?>>Bahan Baku</option>
                    <option value="Bahan Kemas" <?= ($old['jenis_depo'] === 'Bahan Kemas') ? 'selected' : '' ?>>Bahan Kemas</option>
                    <option value="Distribusi" <?= ($old['jenis_depo'] === 'Distribusi') ? 'selected' : '' ?>>Distribusi</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="nama_depo" class="form-label">Nama Fasilitas Depo <span class="text-danger">*</span></label>
            <input type="text" id="nama_depo" name="nama_depo" class="form-input" value="<?= e($old['nama_depo']) ?>" required>
        </div>

        <div class="form-group">
            <label for="lokasi" class="form-label">Alamat / Lokasi Operasional <span class="text-danger">*</span></label>
            <textarea id="lokasi" name="lokasi" class="form-textarea" rows="3" required><?= e($old['lokasi']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="status" class="form-label">Status Operasional <span class="text-danger">*</span></label>
            <select id="status" name="status" class="form-select" required>
                <option value="Aktif" <?= ($old['status'] === 'Aktif') ? 'selected' : '' ?>>Aktif</option>
                <option value="Nonaktif" <?= ($old['status'] === 'Nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px;">
            <a href="<?= base_url('?page=depo') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
<!-- bagian nadhifatul hilmy ns end -->

