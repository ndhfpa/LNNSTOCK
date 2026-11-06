<!-- bagian bersama start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Pengguna</h1>
        <p class="page-subtitle">Perbarui data akun dan hak akses: <?= e($user['nama_lengkap']) ?> (<?= e($user['username']) ?>)</p>
    </div>
    <div>
        <a href="<?= base_url('?page=pengguna') ?>" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Kelola Pengguna</span>
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

<div class="card" style="max-width: 680px; margin: 0 auto;">
    <form action="<?= base_url('?page=pengguna&action=edit&id=' . $user['id']) ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" id="username" name="username" class="form-input" value="<?= e($old['username']) ?>" required>
        </div>

        <div class="form-group">
            <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-input" value="<?= e($old['nama_lengkap']) ?>" required>
        </div>

        <div class="form-group">
            <label for="role" class="form-label">Hak Akses (Role) <span class="text-danger">*</span></label>
            <select id="role" name="role" class="form-select" required>
                <option value="Petugas Gudang" <?= ($old['role'] === 'Petugas Gudang') ? 'selected' : '' ?>>Petugas Gudang (Akses Gudang &amp; Input Barang Masuk)</option>
                <option value="Staff Distribusi" <?= ($old['role'] === 'Staff Distribusi') ? 'selected' : '' ?>>Staff Distribusi (Akses Mutasi &amp; Katalog)</option>
                <option value="Kepala Gudang" <?= ($old['role'] === 'Kepala Gudang') ? 'selected' : '' ?>>Kepala Gudang (Akses Penuh Seluruh Modul &amp; Pengguna)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Ganti Password (Opsional)</label>
            <div class="password-field-wrap">
                <input type="password" id="password" name="password" class="form-input password-toggle-input" placeholder="Biarkan kosong jika password tidak diubah">
                <button type="button" class="btn-password-eye" aria-label="Lihat Password">
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            <div class="form-help">Isi hanya jika ingin mengganti password (minimal 8 karakter).</div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px;">
            <a href="<?= base_url('?page=pengguna') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
<!-- bagian bersama end -->

