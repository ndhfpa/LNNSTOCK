<?php
// bagian bersama start

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Menampilkan formulir login dan memproses otentikasi
     */
    public function login(): void
    {
        AuthGuard::guest();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $password === '') {
                $this->flash('error', 'Username dan password wajib diisi.');
                $this->redirect(base_url('?page=auth&action=login'));
            }

            $user = $this->userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                // Regenerasi session ID untuk mencegah session fixation attack
                session_regenerate_id(true);

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'nama_lengkap' => $user['nama_lengkap'],
                    'role' => $user['role'],
                ];

                $this->flash('success', 'Selamat datang kembali, ' . $user['nama_lengkap'] . '!');
                $this->redirect(base_url('?page=dashboard'));
            } else {
                $this->flash('error', 'Username atau password yang Anda masukkan salah.');
                $this->redirect(base_url('?page=auth&action=login'));
            }
        }

        // Tampilkan halaman login tanpa layout utama
        $this->view('auth/login', [
            'pageTitle' => 'Masuk - LNNSTOCK WMS'
        ], false);
    }

    /**
     * Memproses keluar sesi (logout) dengan metode POST + CSRF
     */
    public function logout(): void
    {
        $this->verifyCsrf();

        unset($_SESSION['user']);
        session_regenerate_id(true);

        $this->flash('success', 'Anda telah berhasil keluar dari sistem.');
        $this->redirect(base_url('?page=auth&action=login'));
    }
}

// bagian bersama end

