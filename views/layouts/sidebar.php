<!-- bagian bersama start -->
<aside class="app-sidebar" id="appSidebar">
    <!-- Brand Header -->
    <div class="sidebar-brand">
        <div class="brand-logo-wrap">
            <img src="<?= base_url('assets/img/logo-lnnstock.svg') ?>" alt="LNNSTOCK Logo" class="brand-logo-img">
        </div>
        <div class="brand-corp-meta">
            <div class="brand-company">PT RIYANA CIPTA PANGAN</div>
            <div class="brand-tagline">Manage Smarter, Move Better.</div>
        </div>
    </div>

    <!-- Navigasi Menu -->
    <nav class="sidebar-nav">
        <!-- Grup: UTAMA -->
        <div class="nav-group-label">UTAMA</div>
        <a href="<?= base_url('?page=dashboard') ?>" class="nav-item <?= ($currentPage === 'dashboard') ? 'active' : '' ?>">
            <span class="nav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
            </span>
            <span class="nav-text">Dashboard</span>
        </a>
<!-- bagian bersama end -->

<!-- bagian nadhifatul hilmy ns start -->
        <?php if (Permission::can('depo') || Permission::can('rak')): ?>
            <!-- Grup: MANAJEMEN DEPO -->
            <div class="nav-group-label">MANAJEMEN DEPO</div>
            <?php if (Permission::can('depo')): ?>
                <a href="<?= base_url('?page=depo') ?>" class="nav-item <?= ($currentPage === 'depo') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 21h18M3 7v14M21 7v14M6 21V11a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10"></path>
                            <path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"></path>
                        </svg>
                    </span>
                    <span class="nav-text">Master Gudang</span>
                </a>
            <?php endif; ?>
            <?php if (Permission::can('rak')): ?>
                <a href="<?= base_url('?page=rak') ?>" class="nav-item <?= ($currentPage === 'rak') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16v16H4zM4 10h16M4 16h16M10 4v16M16 4v16"></path>
                        </svg>
                    </span>
                    <span class="nav-text">Denah Rak</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
<!-- bagian nadhifatul hilmy ns end -->

<!-- bagian lutfi firmansyah start -->
        <!-- Grup: INVENTORI FMCG -->
        <div class="nav-group-label">INVENTORI FMCG</div>
        <a href="<?= base_url('?page=produk') ?>" class="nav-item <?= ($currentPage === 'produk') ? 'active' : '' ?>">
            <span class="nav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                    <path d="m3.3 7 8.7 5 8.7-5M12 22V12"></path>
                </svg>
            </span>
            <span class="nav-text">Katalog Produk</span>
        </a>
<!-- bagian lutfi firmansyah end -->

<!-- bagian nurul khoyimah start -->
        <?php if (Permission::can('mutasi')): ?>
            <a href="<?= base_url('?page=mutasi') ?>" class="nav-item <?= ($currentPage === 'mutasi') ? 'active' : '' ?>">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m16 3 4 4-4 4M20 7H4M8 21l-4-4 4-4M4 17h16"></path>
                    </svg>
                </span>
                <span class="nav-text">Mutasi Antar-Depo</span>
                <?php if (!empty($pendingMutasiCount) && $pendingMutasiCount > 0): ?>
                    <span class="nav-badge badge-pending"><?= (int)$pendingMutasiCount ?> Pending</span>
                <?php endif; ?>
            </a>
        <?php endif; ?>
        <a href="<?= base_url('?page=kedaluwarsa') ?>" class="nav-item <?= ($currentPage === 'kedaluwarsa') ? 'active' : '' ?>">
            <span class="nav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </span>
            <span class="nav-text">Monitoring Kedaluwarsa</span>
            <span class="nav-badge badge-fefo">FEFO</span>
        </a>
<!-- bagian nurul khoyimah end -->

<!-- bagian bersama start -->
        <?php if (Permission::can('pengguna')): ?>
            <!-- Grup: SISTEM PENGGUNA -->
            <div class="nav-group-label">PENGGUNA SISTEM</div>
            <a href="<?= base_url('?page=pengguna') ?>" class="nav-item <?= ($currentPage === 'pengguna') ? 'active' : '' ?>">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </span>
                <span class="nav-text">Kelola Pengguna</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Sidebar Bottom: Logout -->
    <div class="sidebar-bottom">
        <form action="<?= base_url('?page=auth&action=logout') ?>" method="POST" class="logout-form">
            <?= csrf_field() ?>
            <button type="submit" class="btn-logout" title="Keluar dari sistem">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Keluar Sesi</span>
            </button>
        </form>
    </div>
</aside>
<!-- bagian bersama end -->

