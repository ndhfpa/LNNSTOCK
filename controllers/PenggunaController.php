<?php
// bagian bersama start

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class PenggunaController extends Controller {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Halaman Daftar Pengguna Sistem (?page=pengguna)
     */
    public function index(): void {
        AuthGuard::check();

        $users = $this->userModel->getAll();

        $this->view('pengguna/index', [
            'pageTitle' => 'Kelola Pengguna Sistem',
            'users'     => $users,
        ]);
    }

    /**
     * Formulir dan proses Tambah Pengguna Baru (?page=pengguna&action=create)
     */
    public function create(): void {
        AuthGuard::check();

        $errors = [];
        $old = [
            'username'     => '',
            'nama_lengkap' => '',
            'role'         => 'Petugas Gudang',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $old['username']     = trim($_POST['username'] ?? '');
            $old['nama_lengkap'] = trim($_POST['nama_lengkap'] ?? '');
            $old['role']         = trim($_POST['role'] ?? 'Petugas Gudang');
            $password            = $_POST['password'] ?? '';

            if ($old['username'] === '') {
                $errors[] = 'Username wajib diisi.';
            } elseif ($this->userModel->isUsernameExists($old['username'])) {
                $errors[] = 'Username ' . e($old['username']) . ' sudah digunakan.';
            }

            if ($old['nama_lengkap'] === '') {
                $errors[] = 'Nama lengkap wajib diisi.';
            }

            $allowedRoles = ['Kepala Gudang', 'Petugas Gudang', 'Staff Distribusi'];
            if (!in_array($old['role'], $allowedRoles, true)) {
                $errors[] = 'Pilihan role pengguna tidak valid.';
            }

            if ($password === '') {
                $errors[] = 'Password akun wajib diisi.';
            } elseif (strlen($password) < 8) {
                $errors[] = 'Password minimal harus 8 karakter.';
            }

            if (empty($errors)) {
                $data = $old;
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
                $this->userModel->create($data);
                $this->flash('success', 'Pengguna ' . $old['nama_lengkap'] . ' berhasil ditambahkan.');
                $this->redirect(base_url('?page=pengguna'));
            }
        }

        $this->view('pengguna/create', [
            'pageTitle' => 'Tambah Pengguna Baru',
            'errors'    => $errors,
            'old'       => $old,
        ]);
    }

    /**
     * Formulir dan proses Edit Pengguna (?page=pengguna&action=edit&id=<id>)
     */
    public function edit(): void {
        AuthGuard::check();

        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->getById($id);

        if (!$user) {
            $this->flash('error', 'Data pengguna tidak ditemukan.');
            $this->redirect(base_url('?page=pengguna'));
        }

        $errors = [];
        $old = [
            'username'     => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role'         => $user['role'],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $old['username']     = trim($_POST['username'] ?? '');
            $old['nama_lengkap'] = trim($_POST['nama_lengkap'] ?? '');
            $old['role']         = trim($_POST['role'] ?? '');
            $password            = $_POST['password'] ?? '';

            if ($old['username'] === '') {
                $errors[] = 'Username wajib diisi.';
            } elseif ($this->userModel->isUsernameExists($old['username'], $id)) {
                $errors[] = 'Username ' . e($old['username']) . ' sudah digunakan pengguna lain.';
            }

            if ($old['nama_lengkap'] === '') {
                $errors[] = 'Nama lengkap wajib diisi.';
            }

            $allowedRoles = ['Kepala Gudang', 'Petugas Gudang', 'Staff Distribusi'];
            if (!in_array($old['role'], $allowedRoles, true)) {
                $errors[] = 'Pilihan role pengguna tidak valid.';
            }

            // Proteksi: Tidak boleh menurunkan role Kepala Gudang terakhir
            if ($user['role'] === 'Kepala Gudang' && $old['role'] !== 'Kepala Gudang') {
                if ($this->userModel->countByRole('Kepala Gudang') <= 1) {
                    $errors[] = 'Tidak dapat mengubah role Kepala Gudang terakhir. Harus ada minimal satu akun Kepala Gudang.';
                }
            }

            if ($password !== '' && strlen($password) < 8) {
                $errors[] = 'Password baru minimal harus 8 karakter.';
            }

            if (empty($errors)) {
                $updateData = [
                    'username'     => $old['username'],
                    'nama_lengkap' => $old['nama_lengkap'],
                    'role'         => $old['role'],
                ];
                if ($password !== '') {
                    $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
                }

                $this->userModel->update($id, $updateData);

                // Perbarui sesi jika akun yang diedit adalah akun yang sedang aktif login
                if ($id === (int)($_SESSION['user']['id'] ?? 0)) {
                    $_SESSION['user']['nama_lengkap'] = $old['nama_lengkap'];
                    $_SESSION['user']['username']     = $old['username'];
                    $_SESSION['user']['role']         = $old['role'];
                }

                $this->flash('success', 'Data pengguna ' . $old['nama_lengkap'] . ' berhasil diperbarui.');
                $this->redirect(base_url('?page=pengguna'));
            }
        }

        $this->view('pengguna/edit', [
            'pageTitle' => 'Edit Pengguna',
            'user'      => $user,
            'errors'    => $errors,
            'old'       => $old,
        ]);
    }

    /**
     * Proses Hapus Pengguna (POST + CSRF) (?page=pengguna&action=delete)
     */
    public function delete(): void {
        AuthGuard::check();
        $this->verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $user = $this->userModel->getById($id);

        if (!$user) {
            $this->flash('error', 'Pengguna tidak ditemukan.');
            $this->redirect(base_url('?page=pengguna'));
        }

        // 1. Tidak boleh menghapus akun sendiri
        if ($id === (int)($_SESSION['user']['id'] ?? 0)) {
            $this->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            $this->redirect(base_url('?page=pengguna'));
        }

        // 2. Tidak boleh menghapus Kepala Gudang terakhir
        if ($user['role'] === 'Kepala Gudang' && $this->userModel->countByRole('Kepala Gudang') <= 1) {
            $this->flash('error', 'Tidak dapat menghapus akun Kepala Gudang terakhir. Sistem membutuhkan minimal satu Kepala Gudang.');
            $this->redirect(base_url('?page=pengguna'));
        }

        // 3. Pengguna yang sudah punya riwayat mutasi tidak boleh dihapus
        if ($this->userModel->hasMutasi($id)) {
            $this->flash('error', 'Pengguna "' . $user['nama_lengkap'] . '" tidak dapat dihapus karena memiliki riwayat pembuatan transaksi mutasi persediaan.');
            $this->redirect(base_url('?page=pengguna'));
        }

        $this->userModel->delete($id);
        $this->flash('success', 'Pengguna ' . $user['nama_lengkap'] . ' berhasil dihapus.');
        $this->redirect(base_url('?page=pengguna'));
    }
}

// bagian bersama end

