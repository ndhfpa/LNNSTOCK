<!-- bagian nadhifatul hilmy ns start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Rak Gudang Baru</h1>
        <p class="page-subtitle">Alokasikan slot rak multi-tier pada fasilitas penyimpanan</p>
    </div>
    <div>
        <a href="<?= base_url('?page=rak&depo=' . $old['id_depo']) ?>" class="btn btn-secondary">
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
    <form action="<?= base_url('?page=rak&action=create') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="id_depo" class="form-label">Depo Gudang <span class="text-danger">*</span></label>
            <select id="id_depo" name="id_depo" class="form-select" required>
                <?php foreach ($depos as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($old['id_depo'] == $d['id']) ? 'selected' : '' ?>>
                        <?= e($d['kode_depo']) ?> - <?= e($d['nama_depo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="kode_rak" class="form-label">Kode Rak <span class="text-danger">*</span></label>
                <input type="text" id="kode_rak" name="kode_rak" class="form-input" placeholder="Cth: A-04 atau B-02" value="<?= e($old['kode_rak']) ?>" required autofocus>
                <div class="form-help">Kode rak unik dalam satu depo.</div>
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
                <input type="number" id="kapasitas" name="kapasitas" class="form-input" min="1" placeholder="Cth: 500" value="<?= e($old['kapasitas']) ?>" required>
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
            <a href="<?= base_url('?page=rak&depo=' . $old['id_depo']) ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Rak</button>
        </div>
    </form>
</div>
<!-- bagian nadhifatul hilmy ns end -->

