<!-- bagian bersama start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Profil Akun Saya</h1>
        <p class="page-subtitle">Kelola informasi identitas pribadi dan keamanan kata sandi akun LNNSTOCK Anda</p>
    </div>
</div>

<div class="form-grid-2">
    <!-- Panel 1: Data Identitas Pengguna -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--primary); margin-bottom: 6px;">Informasi Identitas</h3>
        <p class="text-muted" style="font-size: 12px; margin-bottom: 20px;">Username dan peran operasional Anda terdaftar pada sistem PT Riyana Cipta Pangan Indonesia.</p>

        <?php if (!empty($profileErrors)): ?>
            <div class="alert-banner alert-danger">
                <ul style="margin-left: 20px;">
                    <?php foreach ($profileErrors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('?page=profil') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="form_type" value="profile">

            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" class="form-input" style="background-color: #F8FAFC; color: #64748B;" value="<?= e($user['username']) ?>" readonly disabled>
                <div class="form-help">Username ditetapkan permanen oleh administrator.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Peran Operasional (Role)</label>
                <div>
                    <?php
                        $roleBadge = 'badge-blue';
                        if ($user['role'] === 'Kepala Gudang') $roleBadge = 'badge-green';
                        elseif ($user['role'] === 'Petugas Gudang') $roleBadge = 'badge-yellow';
                        elseif ($user['role'] === 'Staff Distribusi') $roleBadge = 'badge-blue';
                    ?>
                    <span class="badge <?= $roleBadge ?>" style="font-size: 13px; padding: 6px 12px;"><?= e($user['role']) ?></span>
                </div>
            </div>

            <div class="form-group">
                <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-input" value="<?= e($user['nama_lengkap']) ?>" required>
                <div class="form-help">Nama lengkap yang akan ditampilkan pada topbar dan riwayat aktivitas sistem.</div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>Simpan Perubahan Nama</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Panel 2: Ganti Kata Sandi -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--primary); margin-bottom: 6px;">Keamanan Kata Sandi</h3>
        <p class="text-muted" style="font-size: 12px; margin-bottom: 20px;">Ganti kata sandi secara berkala untuk menjaga keamanan akses inventori gudang.</p>

        <?php if (!empty($passwordErrors)): ?>
            <div class="alert-banner alert-danger">
                <ul style="margin-left: 20px;">
                    <?php foreach ($passwordErrors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('?page=profil') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="form_type" value="password">

            <div class="form-group">
                <label for="password_lama" class="form-label">Kata Sandi Saat Ini <span class="text-danger">*</span></label>
                <div class="password-field-wrap">
                    <input type="password" id="password_lama" name="password_lama" class="form-input password-toggle-input" placeholder="Masukkan sandi saat ini" required>
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
            </div>

            <div class="form-group">
                <label for="password_baru" class="form-label">Kata Sandi Baru <span class="text-danger">*</span></label>
                <div class="password-field-wrap">
                    <input type="password" id="password_baru" name="password_baru" class="form-input password-toggle-input" placeholder="Minimal 8 karakter" required>
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
                <div class="form-help">Minimal 8 karakter. Disarankan perpaduan huruf dan angka.</div>
            </div>

            <div class="form-group">
                <label for="konfirmasi_password" class="form-label">Konfirmasi Kata Sandi Baru <span class="text-danger">*</span></label>
                <div class="password-field-wrap">
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="form-input password-toggle-input" placeholder="Ulangi kata sandi baru" required>
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
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
                <button type="submit" class="btn btn-secondary" style="border-color: #075B75; color: #075B75;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span>Perbarui Kata Sandi</span>
                </button>
            </div>
        </form>
    </div>
</div>
<!-- bagian bersama end -->

