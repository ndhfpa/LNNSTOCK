<?php
// bagian bersama start

class AuthGuard {
    /**
     * Memastikan pengguna sudah terautentikasi (login)
     * Jika belum, alihkan ke halaman login
     */
    public static function check(): void {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . base_url('?page=auth&action=login'));
            exit;
        }
    }

    /**
     * Memastikan pengguna belum login (khusus halaman login)
     * Jika sudah login, alihkan ke dashboard
     */
    public static function guest(): void {
        if (isset($_SESSION['user'])) {
            header('Location: ' . base_url('?page=dashboard'));
            exit;
        }
    }

    /**
     * Mengambil data pengguna aktif dari sesi
     */
    public static function user(): ?array {
        return $_SESSION['user'] ?? null;
    }
}

// bagian bersama end

