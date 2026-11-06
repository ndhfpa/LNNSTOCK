<?php
// bagian bersama start

session_start();

// Muat komponen inti MVC
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/AuthGuard.php';
require_once __DIR__ . '/../core/Permission.php';

// Ambil parameter rute dari URL (?page=<modul>&action=<aksi>)
$page   = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Whitelist halaman yang diizinkan dalam sistem
$allowedPages = ['auth', 'dashboard', 'depo', 'rak', 'produk', 'kedaluwarsa', 'mutasi', 'profil', 'pengguna'];

if (!in_array($page, $allowedPages, true)) {
    http_response_code(404);
    die('Halaman tidak ditemukan (Error 404). Pastikan URL yang dituju sudah terdaftar di whitelist sistem.');
}

try {
    // 1. Rute Otentikasi & Sesi Pengguna
    if ($page === 'auth') {
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController();
        if ($action === 'login') {
            $controller->login();
        } elseif ($action === 'logout') {
            $controller->logout();
        } else {
            $controller->login();
        }
        exit;
    }

    // Pastikan pengguna sudah terautentikasi untuk seluruh halaman selain auth
    AuthGuard::check();

    // Pengecekan hak akses (role permissions)
    if (!Permission::can($page, $action)) {
        flash('error', 'Anda tidak punya akses ke halaman tersebut.');
        header('Location: ' . base_url('?page=dashboard'));
        exit;
    }

    // 2. Rute Dashboard Utama
    if ($page === 'dashboard') {
        require_once __DIR__ . '/../controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        exit;
    }

    // 2b. Rute Edit Profil Akun Saya
    if ($page === 'profil') {
        require_once __DIR__ . '/../controllers/ProfilController.php';
        $controller = new ProfilController();
        $controller->index();
        exit;
    }

    // 2c. Rute Kelola Pengguna (Khusus Kepala Gudang)
    if ($page === 'pengguna') {
        require_once __DIR__ . '/../controllers/PenggunaController.php';
        $controller = new PenggunaController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        exit;
    }
// bagian bersama end

// bagian nadhifatul hilmy ns start
    // 3. Rute Modul Depo & Denah Rak
    if ($page === 'depo') {
        require_once __DIR__ . '/../controllers/DepoController.php';
        $controller = new DepoController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        exit;
    }

    if ($page === 'rak') {
        require_once __DIR__ . '/../controllers/RakController.php';
        $controller = new RakController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        exit;
    }
// bagian nadhifatul hilmy ns end

// bagian lutfi firmansyah start
    // 4. Rute Modul Katalog Produk & Buffer Stock
    if ($page === 'produk') {
        require_once __DIR__ . '/../controllers/ProdukController.php';
        $controller = new ProdukController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        exit;
    }
// bagian lutfi firmansyah end

// bagian nurul khoyimah start
    // 5. Rute Modul Monitoring Kedaluwarsa & Mutasi Antar-Depo
    if ($page === 'kedaluwarsa') {
        require_once __DIR__ . '/../controllers/BatchController.php';
        $controller = new BatchController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        exit;
    }

    if ($page === 'mutasi') {
        require_once __DIR__ . '/../controllers/MutasiController.php';
        $controller = new MutasiController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'complete') {
            $controller->complete();
        } elseif ($action === 'cancel') {
            $controller->cancel();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        exit;
    }
// bagian nurul khoyimah end

// bagian bersama start
} catch (Throwable $e) {
    // Catat seluruh pengecualian ke log sistem server demi keamanan
    error_log("LNNSTOCK Application Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    echo '<div style="font-family: sans-serif; padding: 40px; text-align: center; color: #334155;">';
    echo '<h2 style="color: #075B75;">LNNSTOCK WMS Platform</h2>';
    echo '<p style="color: #DC2626; font-weight: 600;">Maaf, terjadi kesalahan saat memproses permintaan Anda.</p>';
    echo '<p style="font-size: 13px; color: #64748B;">Pesan error telah dicatat ke log server. Silakan hubungi tim administrator sistem.</p>';
    echo '<a href="' . base_url('?page=dashboard') . '" style="display:inline-block; margin-top:16px; padding:8px 16px; background:#075B75; color:#fff; text-decoration:none; border-radius:8px;">Kembali ke Dashboard</a>';
    echo '</div>';
    exit;
}
// bagian bersama end

