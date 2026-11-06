<!-- bagian nadhifatul hilmy ns start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Rak Gudang</h1>
        <p class="page-subtitle">Perbarui data rak <?= e($rak['kode_rak']) ?> pada depo <?= e($rak['nama_depo']) ?></p>
    </div>
    <div>
        <a href="<?= base_url('?page=rak&depo=' . $rak['id_depo']) ?>" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Denah Rak</span>
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
    <div style="background-color: #F8FAFC; border: 1px solid var(--border-light); padding: 14px 18px; border-radius: 12px; margin-bottom: 24px;">
        <div style="font-size: 13px; color: #475569;">
            Depo: <strong><?= e($rak['nama_depo']) ?> (<?= e($rak['kode_depo']) ?>)</strong> &bull;
            Muatan Fisik Saat Ini: <strong style="color:var(--primary);"><?= $currentLoad ?> Karton</strong>
        </div>
    </div>

    <form action="<?= base_url('?page=rak&action=edit&id=' . $rak['id']) ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="kode_rak" class="form-label">Kode Rak <span class="text-danger">*</span></label>
                <input type="text" id="kode_rak" name="kode_rak" class="form-input" value="<?= e($old['kode_rak']) ?>" required>
            </div>

            <div class="form-group">
                <label for="zona" class="form-label">Zona Rak <span class="text-danger">*</span></label>
                <select id="zona" name="zona" class="form-select" required>
                    <option value="A" <?= ($old['zona'] === 'A') ? 'selected' : '' ?>>Zona A (Fast Moving)</option>
                    <option value="B" <?= ($old['zona'] === 'B') ? 'selected' : '' ?>>Zona B (Medium Moving)</option>
                    <option value="C" <?= ($old['zona'] === 'C') ? 'selected' : '' ?>>Zona C (Slow Moving / Buffer)</option>
                </select>
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="kapasitas" class="form-label">Kapasitas Rak (Karton) <span class="text-danger">*</span></label>
                <input type="number" id="kapasitas" name="kapasitas" class="form-input" min="<?= max(1, $currentLoad) ?>" value="<?= e($old['kapasitas']) ?>" required>
                <div class="form-help">Minimal tidak boleh kurang dari isi barang saat ini (<?= $currentLoad ?> CTN).</div>
            </div>

            <div class="form-group">
                <label for="status_rak" class="form-label">Status Rak <span class="text-danger">*</span></label>
                <select id="status_rak" name="status_rak" class="form-select" required>
                    <option value="Aktif" <?= ($old['status_rak'] === 'Aktif') ? 'selected' : '' ?>>Aktif (Bisa Diisi)</option>
                    <option value="Maintenance" <?= ($old['status_rak'] === 'Maintenance') ? 'selected' : '' ?>>Maintenance (Perbaikan)</option>
                </select>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px;">
            <a href="<?= base_url('?page=rak&depo=' . $rak['id_depo']) ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
<!-- bagian nadhifatul hilmy ns end -->

