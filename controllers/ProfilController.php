<?php
// bagian bersama start

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class ProfilController extends Controller {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Halaman Edit Profil Akun Saya (?page=profil)
     */
    public function index(): void {
        AuthGuard::check();

        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $user = $this->userModel->getById($userId);

        if (!$user) {
            $this->flash('error', 'Data akun tidak ditemukan.');
            $this->redirect(base_url('?page=dashboard'));
        }

        $profileErrors = [];
        $passwordErrors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $formType = trim($_POST['form_type'] ?? '');

            // 1. Pembaruan Informasi Profil (Nama Lengkap)
            if ($formType === 'profile') {
                $namaLengkap = trim($_POST['nama_lengkap'] ?? '');
                if ($namaLengkap === '') {
                    $profileErrors[] = 'Nama lengkap wajib diisi.';
                }

                if (empty($profileErrors)) {
                    $this->userModel->updateProfile($userId, $namaLengkap);
                    $_SESSION['user']['nama_lengkap'] = $namaLengkap;
                    $this->flash('success', 'Profil nama lengkap Anda berhasil diperbarui.');
                    $this->redirect(base_url('?page=profil'));
                }
            }

            // 2. Pembaruan Kata Sandi
            if ($formType === 'password') {
                $passwordLama       = $_POST['password_lama'] ?? '';
                $passwordBaru       = $_POST['password_baru'] ?? '';
                $konfirmasiPassword = $_POST['konfirmasi_password'] ?? '';

                if ($passwordLama === '') {
                    $passwordErrors[] = 'Kata sandi lama wajib diisi.';
                } elseif (!password_verify($passwordLama, $user['password'])) {
                    $passwordErrors[] = 'Kata sandi lama yang Anda masukkan tidak sesuai.';
                }

                if ($passwordBaru === '') {
                    $passwordErrors[] = 'Kata sandi baru wajib diisi.';
                } elseif (strlen($passwordBaru) < 8) {
                    $passwordErrors[] = 'Kata sandi baru minimal harus 8 karakter.';
                }

                if ($passwordBaru !== $konfirmasiPassword) {
                    $passwordErrors[] = 'Konfirmasi kata sandi baru tidak cocok.';
                }

                if (empty($passwordErrors)) {
                    $hash = password_hash($passwordBaru, PASSWORD_BCRYPT);
                    $this->userModel->updatePassword($userId, $hash);
                    $this->flash('success', 'Kata sandi akun Anda berhasil diperbarui.');
                    $this->redirect(base_url('?page=profil'));
                }
            }
        }

        $this->view('profil/index', [
            'pageTitle'      => 'Profil Akun Saya',
            'user'           => $user,
            'profileErrors'  => $profileErrors,
            'passwordErrors' => $passwordErrors,
        ]);
    }
}

// bagian bersama end

