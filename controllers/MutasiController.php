<?php
// bagian nurul khoyimah start

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Mutasi.php';
require_once __DIR__ . '/../models/Batch.php';
require_once __DIR__ . '/../models/Depo.php';

class MutasiController extends Controller {
    private Mutasi $mutasiModel;
    private Batch $batchModel;
    private Depo $depoModel;

    public function __construct() {
        $this->mutasiModel = new Mutasi();
        $this->batchModel = new Batch();
        $this->depoModel = new Depo();
    }

    /**
     * Halaman Manajemen Mutasi Stok Antar-Depo (?page=mutasi)
     */
    public function index(): void {
        AuthGuard::check();

        $statusFilter = trim($_GET['status'] ?? 'Semua');
        $mutasis = $this->mutasiModel->getAll($statusFilter);
        $kpi = $this->mutasiModel->getKpi();

        $this->view('mutasi/index', [
            'pageTitle'    => 'Manajemen Mutasi Stok Antar-Depo',
            'mutasis'      => $mutasis,
            'kpi'          => $kpi,
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Formulir dan proses Buat Surat Perintah Mutasi (SPM) Baru (?page=mutasi&action=create)
     */
    public function create(): void {
        AuthGuard::check();

        $batches = $this->batchModel->getBatchesForMutasi();
        $depos = $this->depoModel->getAll();
        $preBatchId = (int)($_GET['batch_id'] ?? 0);
        $autoNoMutasi = $this->mutasiModel->generateNoMutasi();

        $errors = [];
        $old = [
            'no_mutasi'       => $autoNoMutasi,
            'id_batch'        => $preBatchId ?: ($batches[0]['id'] ?? 0),
            'id_depo_tujuan'  => '',
            'jumlah'          => 50,
            'tanggal'         => date('Y-m-d'),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $old['no_mutasi']       = trim($_POST['no_mutasi'] ?? $autoNoMutasi);
            $old['id_batch']        = (int)($_POST['id_batch'] ?? 0);
            $old['id_depo_tujuan']  = (int)($_POST['id_depo_tujuan'] ?? 0);
            $old['jumlah']          = (int)($_POST['jumlah'] ?? 0);
            $old['tanggal']         = trim($_POST['tanggal'] ?? date('Y-m-d'));
            $old['id_user']         = (int)(AuthGuard::user()['id'] ?? 1);

            $selectedBatch = $this->batchModel->getById($old['id_batch']);

            if (!$selectedBatch) {
                $errors[] = 'Batch persediaan asal wajib dipilih.';
            } else {
                if ($old['id_depo_tujuan'] <= 0) {
                    $errors[] = 'Depo tujuan pemindahan stok wajib dipilih.';
                } elseif ((int)$selectedBatch['id_depo'] === $old['id_depo_tujuan']) {
                    $errors[] = 'Depo tujuan tidak boleh sama dengan depo asal batch persediaan.';
                }

                if ($old['jumlah'] <= 0) {
                    $errors[] = 'Jumlah karton yang dipindahkan harus minimal 1 CTN.';
                } elseif ($old['jumlah'] > (int)$selectedBatch['jumlah']) {
                    $errors[] = 'Jumlah mutasi melebihi sisa stok fisik yang tersedia (' . $selectedBatch['jumlah'] . ' CTN).';
                }
            }

            if (empty($errors)) {
                $this->mutasiModel->create($old);
                $this->flash('success', 'Surat Perintah Mutasi ' . $old['no_mutasi'] . ' berhasil diterbitkan dan siap diverifikasi.');
                $this->redirect(base_url('?page=mutasi'));
            }
        }

        $this->view('mutasi/create', [
            'pageTitle'    => 'Buat Surat Perintah Mutasi Baru',
            'batches'      => $batches,
            'depos'        => $depos,
            'autoNoMutasi' => $autoNoMutasi,
            'preBatchId'   => $preBatchId,
            'errors'       => $errors,
            'old'          => $old,
        ]);
    }

    /**
     * Setujui dan Selesaikan Mutasi Stok (POST + CSRF) (?page=mutasi&action=complete)
     */
    public function complete(): void {
        AuthGuard::check();
        $this->verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $result = $this->mutasiModel->complete($id);

        if ($result['success']) {
            $this->flash('success', $result['message']);
        } else {
            $this->flash('error', $result['message']);
        }

        $this->redirect(base_url('?page=mutasi'));
    }

    /**
     * Batalkan Permintaan Mutasi (POST + CSRF) (?page=mutasi&action=cancel)
     */
    public function cancel(): void {
        AuthGuard::check();
        $this->verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $mutasi = $this->mutasiModel->getById($id);

        if (!$mutasi) {
            $this->flash('error', 'Data mutasi tidak ditemukan.');
        } elseif ($mutasi['status'] !== 'Menunggu') {
            $this->flash('error', 'Hanya mutasi berstatus Menunggu yang dapat dibatalkan.');
        } else {
            $this->mutasiModel->cancel($id);
            $this->flash('success', 'Mutasi ' . $mutasi['no_mutasi'] . ' berhasil dibatalkan.');
        }

        $this->redirect(base_url('?page=mutasi'));
    }

    /**
     * Hapus Data Mutasi (POST + CSRF) (?page=mutasi&action=delete)
     */
    public function delete(): void {
        AuthGuard::check();
        $this->verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $mutasi = $this->mutasiModel->getById($id);

        if (!$mutasi) {
            $this->flash('error', 'Data mutasi tidak ditemukan.');
        } elseif ($mutasi['status'] === 'Selesai') {
            $this->flash('error', 'Mutasi yang sudah Selesai tidak dapat dihapus demi integritas audit stok.');
        } else {
            $this->mutasiModel->delete($id);
            $this->flash('success', 'Mutasi ' . $mutasi['no_mutasi'] . ' berhasil dihapus.');
        }

        $this->redirect(base_url('?page=mutasi'));
    }
}

// bagian nurul khoyimah end

