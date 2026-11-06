<?php
// bagian nadhifatul hilmy ns start

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Depo.php';

class DepoController extends Controller {
    private Depo $depoModel;

    public function __construct() {
        $this->depoModel = new Depo();
    }

    /**
     * Halaman Utama Master Depo Gudang (?page=depo)
     */
    public function index(): void {
        AuthGuard::check();

        $depos = $this->depoModel->getAll();
        $kpi = $this->depoModel->getKpi();

        $this->view('depo/index', [
            'pageTitle' => 'Master Depo Gudang',
            'depos'     => $depos,
            'kpi'       => $kpi,
        ]);
    }

    /**
     * Formulir dan proses Tambah Depo Baru (?page=depo&action=create)
     */
    public function create(): void {
        AuthGuard::check();

        $errors = [];
        $old = [
            'kode_depo'  => '',
            'nama_depo'  => '',
            'jenis_depo' => 'Produk Jadi',
            'lokasi'     => '',
            'status'     => 'Aktif',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $old['kode_depo']  = strtoupper(trim($_POST['kode_depo'] ?? ''));
            $old['nama_depo']  = trim($_POST['nama_depo'] ?? '');
            $old['jenis_depo'] = trim($_POST['jenis_depo'] ?? '');
            $old['lokasi']     = trim($_POST['lokasi'] ?? '');
            $old['status']     = trim($_POST['status'] ?? 'Aktif');

            if ($old['kode_depo'] === '') {
                $errors[] = 'Kode depo wajib diisi.';
            } elseif ($this->depoModel->isKodeExists($old['kode_depo'])) {
                $errors[] = 'Kode depo ' . e($old['kode_depo']) . ' sudah terdaftar.';
            }

            if ($old['nama_depo'] === '') {
                $errors[] = 'Nama depo wajib diisi.';
            }

            if ($old['lokasi'] === '') {
                $errors[] = 'Lokasi depo wajib diisi.';
            }

            if (empty($errors)) {
                $this->depoModel->create($old);
                $this->flash('success', 'Depo ' . $old['nama_depo'] . ' berhasil ditambahkan.');
                $this->redirect(base_url('?page=depo'));
            }
        }

        $this->view('depo/create', [
            'pageTitle' => 'Tambah Depo Baru',
            'errors'    => $errors,
            'old'       => $old,
        ]);
    }

    /**
     * Formulir dan proses Edit Depo (?page=depo&action=edit&id=<id>)
     */
    public function edit(): void {
        AuthGuard::check();

        $id = (int)($_GET['id'] ?? 0);
        $depo = $this->depoModel->getById($id);

        if (!$depo) {
            $this->flash('error', 'Data depo tidak ditemukan.');
            $this->redirect(base_url('?page=depo'));
        }

        $errors = [];
        $old = $depo;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $old['kode_depo']  = strtoupper(trim($_POST['kode_depo'] ?? ''));
            $old['nama_depo']  = trim($_POST['nama_depo'] ?? '');
            $old['jenis_depo'] = trim($_POST['jenis_depo'] ?? '');
            $old['lokasi']     = trim($_POST['lokasi'] ?? '');
            $old['status']     = trim($_POST['status'] ?? 'Aktif');

            if ($old['kode_depo'] === '') {
                $errors[] = 'Kode depo wajib diisi.';
            } elseif ($this->depoModel->isKodeExists($old['kode_depo'], $id)) {
                $errors[] = 'Kode depo ' . e($old['kode_depo']) . ' sudah digunakan oleh depo lain.';
            }

            if ($old['nama_depo'] === '') {
                $errors[] = 'Nama depo wajib diisi.';
            }

            if ($old['lokasi'] === '') {
                $errors[] = 'Lokasi depo wajib diisi.';
            }

            if (empty($errors)) {
                $this->depoModel->update($id, $old);
                $this->flash('success', 'Data depo ' . $old['nama_depo'] . ' berhasil diperbarui.');
                $this->redirect(base_url('?page=depo'));
            }
        }

        $this->view('depo/edit', [
            'pageTitle' => 'Edit Depo Gudang',
            'errors'    => $errors,
            'old'       => $old,
            'depo'      => $depo,
        ]);
    }

    /**
     * Proses Hapus Depo (POST + CSRF) (?page=depo&action=delete)
     */
    public function delete(): void {
        AuthGuard::check();
        $this->verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $depo = $this->depoModel->getById($id);

        if (!$depo) {
            $this->flash('error', 'Depo tidak ditemukan.');
            $this->redirect(base_url('?page=depo'));
        }

        try {
            $this->depoModel->delete($id);
            $this->flash('success', 'Depo ' . $depo['nama_depo'] . ' berhasil dihapus.');
        } catch (PDOException $e) {
            // Tangkap kode constraint SQLSTATE 23000
            error_log("Gagal hapus depo: " . $e->getMessage());
            $this->flash('error', 'Gagal menghapus depo: Depo ini masih memiliki rak atau alokasi batch stok. Hapus atau pindahkan rak terlebih dahulu.');
        }

        $this->redirect(base_url('?page=depo'));
    }
}

// bagian nadhifatul hilmy ns end

