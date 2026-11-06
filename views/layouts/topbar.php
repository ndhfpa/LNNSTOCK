<!-- bagian bersama start -->
<div class="app-main">
    <header class="app-topbar">
        <div class="topbar-left">
            <button type="button" class="btn-hamburger" id="btnHamburger" aria-label="Buka Menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <div class="topbar-breadcrumb">
                <span class="bc-brand">LNNSTOCK</span>
                <span class="bc-sep">/</span>
                <span class="bc-current"><?= isset($pageTitle) ? e($pageTitle) : 'Warehouse System' ?></span>
            </div>
        </div>

        <div class="topbar-right">
            <!-- Chip Tanggal Hari Ini -->
            <div class="topbar-chip chip-date" title="Tanggal Operasional Hari Ini">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span><?= format_date_id(date('Y-m-d'), true) ?></span>
            </div>

            <!-- Notifikasi Lonceng Kedaluwarsa Kritis -->
            <a href="<?= base_url('?page=kedaluwarsa') ?>" class="topbar-chip chip-bell" title="Monitoring Kedaluwarsa Kritis (<= 30 hari)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <?php if (!empty($kritisBatchCount) && $kritisBatchCount > 0): ?>
                    <span class="bell-badge"><?= (int)$kritisBatchCount ?></span>
                <?php endif; ?>
            </a>

            <!-- Chip Pengguna Aktif (Buka Profil Akun Saya) -->
            <a href="<?= base_url('?page=profil') ?>" class="topbar-chip chip-user" title="Kelola Profil Akun Saya">
                <div class="user-avatar">
                    <?php
                        $namaParts = explode(' ', $currentUser['nama_lengkap'] ?? 'User');
                        $inisial = strtoupper(substr($namaParts[0] ?? 'U', 0, 1) . substr($namaParts[1] ?? '', 0, 1));
                    ?>
                    <?= e($inisial ?: 'AP') ?>
                </div>
                <div class="user-meta">
                    <span class="user-name"><?= e($currentUser['nama_lengkap'] ?? 'Agung Prayitno') ?></span>
                    <span class="user-role"><?= e($currentUser['role'] ?? 'Kepala Gudang') ?></span>
                </div>
            </a>
        </div>
    </header>

    <!-- Konten Halaman Dinamis -->
    <main class="app-content">
        <!-- Toast / Flash Messages -->
        <?php if (flash_has('success')): ?>
            <div class="alert-banner alert-success" role="alert">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span><?= e(flash_get('success')) ?></span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (flash_has('error')): ?>
            <div class="alert-banner alert-danger" role="alert">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span><?= e(flash_get('error')) ?></span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        <?php endif; ?>
<!-- bagian bersama end -->

