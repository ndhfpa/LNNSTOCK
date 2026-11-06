<!-- bagian bersama start -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - LNNSTOCK WMS PT Riyana Cipta Pangan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <!-- Brand Logo & Header -->
            <div class="login-brand">
                <img src="<?= base_url('assets/img/logo-lnnstock.svg') ?>" alt="LNNSTOCK" class="login-logo-img">
                <h1 class="login-title">LNNSTOCK WMS</h1>
                <p class="login-subtitle">PT Riyana Cipta Pangan Indonesia</p>
                <p class="login-tagline">"Manage Smarter, Move Better."</p>
            </div>

            <!-- Pesan Flash -->
            <?php if (flash_has('error')): ?>
                <div class="alert-banner alert-danger mb-4" role="alert">
                    <span><?= e(flash_get('error')) ?></span>
                </div>
            <?php endif; ?>

            <?php if (flash_has('success')): ?>
                <div class="alert-banner alert-success mb-4" role="alert">
                    <span><?= e(flash_get('success')) ?></span>
                </div>
            <?php endif; ?>

            <!-- Formulir Login -->
            <form action="<?= base_url('?page=auth&action=login') ?>" method="POST" class="login-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-icon-wrap">
                        <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <input type="text" id="username" name="username" class="form-input" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-field-wrap">
                        <div class="input-icon-wrap" style="width: 100%;">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input type="password" id="password" name="password" class="form-input password-toggle-input" placeholder="Masukkan password" required>
                        </div>
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

                <button type="submit" class="btn btn-primary btn-block btn-login">
                    <span>Masuk ke Sistem</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </form>

            <div class="login-footer">
                <div style="text-align: left; background: #F8FAFC; border: 1px solid var(--border-light); border-radius: 10px; padding: 10px 14px; font-size: 11.5px; margin-bottom: 12px;">
                    <div style="font-weight: 700; color: #475569; margin-bottom: 6px;">Akun Uji Coba Multi-Role:</div>
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <div>&bull; <strong>Kepala Gudang:</strong> <code>admin01</code> / <code>Admin123!</code></div>
                        <div>&bull; <strong>Petugas Gudang:</strong> <code>staffGD2</code> / <code>LnnstockGD2</code></div>
                        <div>&bull; <strong>Staff Distribusi:</strong> <code>staffDB3</code> / <code>LnnstockDB3</code></div>
                    </div>
                </div>
                <div class="login-academic">Proyek Akhir Pemrograman Web Dasar &bull; STMIK IKMI Cirebon</div>
            </div>
        </div>
    </div>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
<!-- bagian bersama end -->
