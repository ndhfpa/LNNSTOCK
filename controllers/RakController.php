<?php
// bagian nadhifatul hilmy ns start

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Rak.php';
require_once __DIR__ . '/../models/Depo.php';

class RakController extends Controller {
    private Rak $rakModel;
    private Depo $depoModel;

    public function __construct() {
        $this->rakModel = new Rak();
        $this->depoModel = new Depo();
    }

    /**
     * Halaman Denah Rak & Matriks Multi-Tier (?page=rak&depo=<id>)
     */
    public function index(): void {
        AuthGuard::check();

        $depos = $this->depoModel->getAll();
        if (empty($depos)) {
            $this->flash('error', 'Silakan tambahkan data depo terlebih dahulu.');
            $this->redirect(base_url('?page=depo&action=create'));
        }

        // Tentukan depo aktif (dari URL atau default depo pertama/GD003)
        $depoId = (int)($_GET['depo'] ?? 0);
        $selectedDepo = null;

        if ($depoId > 0) {
            foreach ($depos as $d) {
                if ((int)$d['id'] === $depoId) {
                    $selectedDepo = $d;
                    break;
                }
            }
        }

        // Jika tidak ada parameter atau tidak cocok, pilih GD003 (Produk Jadi) atau depo ke-1
        if (!$selectedDepo) {
            foreach ($depos as $d) {
                if ($d['kode_depo'] === 'GD003') {
                    $selectedDepo = $d;
                    break;
                }
            }
            if (!$selectedDepo) {
                $selectedDepo = $depos[0];
            }
            $depoId = (int)$selectedDepo['id'];
        }

        // Ambil daftar rak pada depo terpilih
        $racks = $this->rakModel->getByDepo($depoId);

        // Ambil daftar batch di setiap rak untuk keperluan modal detail
        $rakPenuhCount = 0;
        $rakTersediaCount = 0;
        $totalKapasitasDepo = 0;
        $totalTerisiDepo = 0;

        foreach ($racks as &$r) {
            $r['batches'] = $this->rakModel->getBatchesInRak((int)$r['id']);
            $totalKapasitasDepo += (int)$r['kapasitas'];
            $totalTerisiDepo += (int)$r['terisi'];

            if ($r['persen_okupansi'] > 90) {
                $rakPenuhCount++;
            } elseif ($r['persen_okupansi'] < 60 && $r['status_rak'] === 'Aktif') {
                $rakTersediaCount++;
            }
        }

        $unassignedStock = $this->rakModel->getUnassignedStock($depoId);

        $this->view('rak/index', [
            'pageTitle'          => 'Denah Rak & Matriks Multi-Tier',
            'depos'              => $depos,
            'selectedDepo'       => $selectedDepo,
            'depoId'             => $depoId,
            'racks'              => $racks,
            'rakPenuhCount'      => $rakPenuhCount,
            'rakTersediaCount'   => $rakTersediaCount,
            'totalKapasitasDepo' => $totalKapasitasDepo,
            'totalTerisiDepo'    => $totalTerisiDepo,
            'unassignedStock'    => $unassignedStock,
        ]);
    }

    /**
     * Formulir dan proses Tambah Rak Baru (?page=rak&action=create)
     */
    public function create(): void {
        AuthGuard::check();

        $depos = $this->depoModel->getAll();
        $targetDepoId = (int)($_GET['depo'] ?? ($depos[0]['id'] ?? 0));

        $errors = [];
        $old = [
            'id_depo'    => $targetDepoId,
            'kode_rak'   => '',
            'zona'       => 'A',
            'kapasitas'  => 500,
            'status_rak' => 'Aktif',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $old['id_depo']    = (int)($_POST['id_depo'] ?? 0);
            $old['kode_rak']   = strtoupper(trim($_POST['kode_rak'] ?? ''));
            $old['zona']       = strtoupper(trim($_POST['zona'] ?? 'A'));
            $old['kapasitas']  = (int)($_POST['kapasitas'] ?? 0);
            $old['status_rak'] = trim($_POST['status_rak'] ?? 'Aktif');

            if ($old['id_depo'] <= 0) {
                $errors[] = 'Depo penempatan wajib dipilih.';
            }

            if ($old['kode_rak'] === '') {
                $errors[] = 'Kode rak wajib diisi (cth: A-01).';
            } elseif ($this->rakModel->isKodeExistsInDepo($old['id_depo'], $old['kode_rak'])) {
                $errors[] = 'Kode rak ' . e($old['kode_rak']) . ' sudah ada di depo yang dipilih.';
            }

            if ($old['kapasitas'] <= 0) {
                $errors[] = 'Kapasitas rak harus lebih besar dari 0 karton.';
            }

            if (empty($errors)) {
                $this->rakModel->create($old);
                $this->flash('success', 'Rak ' . $old['kode_rak'] . ' berhasil ditambahkan.');
                $this->redirect(base_url('?page=rak&depo=' . $old['id_depo']));
            }
        }

        $this->view('rak/create', [
            'pageTitle' => 'Tambah Rak Gudang Baru',
            'depos'     => $depos,
            'errors'    => $errors,
            'old'       => $old,
        ]);
    }

    /**
     * Formulir dan proses Edit Rak (?page=rak&action=edit&id=<id>)
     */
    public function edit(): void {
        AuthGuard::check();

        $id = (int)($_GET['id'] ?? 0);
        $rak = $this->rakModel->getById($id);

        if (!$rak) {
            $this->flash('error', 'Data rak tidak ditemukan.');
            $this->redirect(base_url('?page=rak'));
        }

        $errors = [];
        $old = $rak;
        $currentLoad = $this->rakModel->getCurrentLoad($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $old['kode_rak']   = strtoupper(trim($_POST['kode_rak'] ?? ''));
            $old['zona']       = strtoupper(trim($_POST['zona'] ?? 'A'));
            $old['kapasitas']  = (int)($_POST['kapasitas'] ?? 0);
            $old['status_rak'] = trim($_POST['status_rak'] ?? 'Aktif');

            if ($old['kode_rak'] === '') {
                $errors[] = 'Kode rak wajib diisi.';
            } elseif ($this->rakModel->isKodeExistsInDepo((int)$rak['id_depo'], $old['kode_rak'], $id)) {
                $errors[] = 'Kode rak ' . e($old['kode_rak']) . ' sudah digunakan di depo ini.';
            }

            if ($old['kapasitas'] <= 0) {
                $errors[] = 'Kapasitas rak harus lebih besar dari 0 karton.';
            } elseif ($old['kapasitas'] < $currentLoad) {
                // Aturan: kapasitas tidak boleh diubah menjadi lebih kecil dari isi rak saat ini
                $errors[] = 'Kapasitas tidak boleh lebih kecil dari isi barang saat ini (' . $currentLoad . ' karton).';
            }

            if (empty($errors)) {
                $this->rakModel->update($id, $old);
                $this->flash('success', 'Data rak ' . $old['kode_rak'] . ' berhasil diperbarui.');
                $this->redirect(base_url('?page=rak&depo=' . $rak['id_depo']));
            }
        }

        $this->view('rak/edit', [
            'pageTitle'   => 'Edit Rak Gudang',
            'rak'         => $rak,
            'errors'      => $errors,
            'old'         => $old,
            'currentLoad' => $currentLoad,
        ]);
    }

    /**
     * Proses Hapus Rak (POST + CSRF) (?page=rak&action=delete)
     */
    public function delete(): void {
        AuthGuard::check();
        $this->verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $rak = $this->rakModel->getById($id);

        if (!$rak) {
            $this->flash('error', 'Rak tidak ditemukan.');
            $this->redirect(base_url('?page=rak'));
        }

        $depoId = (int)$rak['id_depo'];
        $currentLoad = $this->rakModel->getCurrentLoad($id);

        if ($currentLoad > 0) {
            $this->flash('error', 'Gagal menghapus: Rak ' . $rak['kode_rak'] . ' masih berisi ' . $currentLoad . ' karton barang. Pindahkan barang terlebih dahulu.');
            $this->redirect(base_url('?page=rak&depo=' . $depoId));
        }

        try {
            $this->rakModel->delete($id);
            $this->flash('success', 'Rak ' . $rak['kode_rak'] . ' berhasil dihapus.');
        } catch (PDOException $e) {
            error_log("Gagal hapus rak: " . $e->getMessage());
            $this->flash('error', 'Gagal menghapus rak: Terdapat relasi data pada batch stok.');
        }

        $this->redirect(base_url('?page=rak&depo=' . $depoId));
    }
}

// bagian nadhifatul hilmy ns end

