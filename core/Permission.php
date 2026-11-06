<?php
// bagian bersama start

class Permission {
    /**
     * Memeriksa hak akses pengguna terhadap modul ($page) dan aksi ($action)
     * berdasarkan role aktif pada sesi pengguna.
     *
     * @param string $page   Nama modul/halaman (contoh: 'depo', 'mutasi', 'pengguna')
     * @param string $action Nama aksi controller (contoh: 'index', 'create', 'edit', 'delete')
     * @return bool True jika diizinkan, False jika ditolak
     */
    public static function can(string $page, string $action = 'index'): bool {
        // Halaman auth (login/logout) selalu diizinkan
        if ($page === 'auth') {
            return true;
        }

        // Jika belum login, serahkan ke AuthGuard
        if (!isset($_SESSION['user']['role'])) {
            return false;
        }

        $role = $_SESSION['user']['role'];

        // Dashboard dan Profil selalu diizinkan untuk semua role yang sudah login
        if ($page === 'dashboard' || $page === 'profil') {
            return true;
        }

        // Normalisasi nama aksi (dukung bahasa Indonesia & bahasa Inggris)
        $actionMap = [
            'selesai' => 'complete',
            'batal'   => 'cancel',
            'hapus'   => 'delete',
            'ubah'    => 'edit',
            'tambah'  => 'create',
            'lihat'   => 'index',
        ];
        $act = $actionMap[strtolower($action)] ?? strtolower($action);

        // 1. KEPALA GUDANG: Memiliki seluruh hak akses
        if ($role === 'Kepala Gudang') {
            return true;
        }

        // 2. PETUGAS GUDANG:
        // - dashboard, profil: boleh
        // - depo, rak, produk, kedaluwarsa: index, create, edit saja (TIDAK boleh delete)
        // - mutasi: TIDAK boleh
        // - pengguna: TIDAK boleh
        if ($role === 'Petugas Gudang') {
            $allowedModules = ['depo', 'rak', 'produk', 'kedaluwarsa'];
            if (in_array($page, $allowedModules, true)) {
                return in_array($act, ['index', 'create', 'edit'], true);
            }
            return false;
        }

        // 3. STAFF DISTRIBUSI:
        // - dashboard, profil: boleh
        // - produk, kedaluwarsa: index saja
        // - mutasi: index, create, edit (TIDAK boleh complete/selesai, cancel/batal, delete)
        // - depo, rak: TIDAK boleh
        // - pengguna: TIDAK boleh
        if ($role === 'Staff Distribusi') {
            if ($page === 'produk' || $page === 'kedaluwarsa') {
                return $act === 'index';
            }
            if ($page === 'mutasi') {
                return in_array($act, ['index', 'create', 'edit'], true);
            }
            return false;
        }

        // Role tidak dikenali
        return false;
    }
}

// bagian bersama end

