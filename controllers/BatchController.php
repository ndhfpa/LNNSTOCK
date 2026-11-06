<?php
// bagian nurul khoyimah start

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Batch.php';
require_once __DIR__ . '/../models/Produk.php';
require_once __DIR__ . '/../models/Depo.php';
require_once __DIR__ . '/../models/Rak.php';

class BatchController extends Controller {
    private Batch $batchModel;
    private Produk $produkModel;
    private Depo $depoModel;
    private Rak $rakModel;

    public function __construct() {
        $this->batchModel = new Batch();
        $this->produkModel = new Produk();
        $this->depoModel = new Depo();
        $this->rakModel = new Rak();
    }

    /**
     * Halaman Monitoring Kedaluwarsa FMCG FEFO & FIFO (?page=kedaluwarsa)
     */
    public function index(): void {
        AuthGuard::check();

        $depoFilter = isset($_GET['depo']) && $_GET['depo'] !== '' ? (int)$_GET['depo'] : null;
        $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? trim($_GET['status']) : null;

        $batches = $this->batchModel->getAll($depoFilter, $statusFilter);
        $kpi = $this->batchModel->getKpi();
        $urgentBatch = $this->batchModel->getMostUrgentBatch();
        $depos = $this->depoModel->getAll();

        $this->view('kedaluwarsa/index', [
            'pageTitle'    => 'Monitoring Kedaluwarsa (FEFO & FIFO)',
            'batches'      => $batches,
            'kpi'          => $kpi,
            'urgentBatch'  => $urgentBatch,
            'depos'        => $depos,
            'depoFilter'   => $depoFilter,
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Formulir dan proses Tambah Batch Stok Baru (?page=kedaluwarsa&action=create)
     */
    public function create(): void {
        AuthGuard::check();

        $produks = $this->produkModel->getAll();
        $depos = $this->depoModel->getAll();

        // Ambil semua rak untuk JavaScript dynamic dropdown
        $stmtRak = Database::connect()->query("SELECT r.*, d.nama_depo FROM rak r JOIN depo d ON d.id = r.id_depo WHERE r.status_rak = 'Aktif' ORDER BY r.kode_rak ASC");
        $allRaks = $stmtRak->fetchAll();

        $errors = [];
        $old = [
            'id_produk'   => $produks[0]['id'] ?? 0,
            'id_depo'     => $depos[0]['id'] ?? 0,
            'id_rak'      => '',
            'no_batch'    => 'BATCH-' . date('Y') . '-',
            'tgl_masuk'   => date('Y-m-d'),
            'tgl_expired' => date('Y-m-d', strtotime('+12 months')),
            'jumlah'      => 100,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $old['id_produk']   = (int)($_POST['id_produk'] ?? 0);
            $old['id_depo']     = (int)($_POST['id_depo'] ?? 0);
            $old['id_rak']      = !empty($_POST['id_rak']) ? (int)$_POST['id_rak'] : null;
            $old['no_batch']    = strtoupper(trim($_POST['no_batch'] ?? ''));
            $old['tgl_masuk']   = trim($_POST['tgl_masuk'] ?? '');
            $old['tgl_expired'] = trim($_POST['tgl_expired'] ?? '');
            $old['jumlah']      = (int)($_POST['jumlah'] ?? 0);

            if ($old['id_produk'] <= 0) $errors[] = 'Produk wajib dipilih.';
            if ($old['id_depo'] <= 0) $errors[] = 'Depo penyimpanan wajib dipilih.';
            if ($old['no_batch'] === '') $errors[] = 'Nomor batch wajib diisi.';
            elseif ($this->batchModel->isBatchExists($old['no_batch'], $old['id_produk'], $old['id_depo'])) {
                $errors[] = 'Nomor batch ' . e($old['no_batch']) . ' sudah ada untuk produk dan depo ini.';
            }

            if ($old['jumlah'] <= 0) {
                $errors[] = 'Jumlah karton harus lebih besar dari 0.';
            }

            if ($old['tgl_expired'] <= $old['tgl_masuk']) {
                $errors[] = 'Tanggal kedaluwarsa harus setelah tanggal masuk barang.';
            }

            // Validasi batas kapasitas rak
            if ($old['id_rak']) {
                $rak = $this->rakModel->getById($old['id_rak']);
                if ($rak) {
                    $currentLoad = $this->rakModel->getCurrentLoad($old['id_rak']);
                    if (($currentLoad + $old['jumlah']) > (int)$rak['kapasitas']) {
                        $sisaSlot = max(0, (int)$rak['kapasitas'] - $currentLoad);
                        $errors[] = 'Kapasitas rak ' . e($rak['kode_rak']) . ' tidak mencukupi (sisa slot: ' . $sisaSlot . ' karton).';
                    }
                }
            }

            if (empty($errors)) {
                $this->batchModel->create($old);
                $this->flash('success', 'Batch ' . $old['no_batch'] . ' berhasil dicatat ke persediaan.');
                $this->redirect(base_url('?page=kedaluwarsa'));
            }
        }

        $this->view('kedaluwarsa/create', [
            'pageTitle' => 'Tambah Batch Stok Baru',
            'produks'   => $produks,
            'depos'     => $depos,
            'allRaks'   => $allRaks,
            'errors'    => $errors,
            'old'       => $old,
        ]);
    }

    /**
     * Formulir dan proses Edit Batch Stok (?page=kedaluwarsa&action=edit&id=<id>)
     */
    public function edit(): void {
        AuthGuard::check();

        $id = (int)($_GET['id'] ?? 0);
        $batch = $this->batchModel->getById($id);

        if (!$batch) {
            $this->flash('error', 'Data batch tidak ditemukan.');
            $this->redirect(base_url('?page=kedaluwarsa'));
        }

        $produks = $this->produkModel->getAll();
        $depos = $this->depoModel->getAll();

        $stmtRak = Database::connect()->query("SELECT r.*, d.nama_depo FROM rak r JOIN depo d ON d.id = r.id_depo WHERE r.status_rak = 'Aktif' ORDER BY r.kode_rak ASC");
        $allRaks = $stmtRak->fetchAll();

        $errors = [];
        $old = $batch;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $old['id_produk']   = (int)($_POST['id_produk'] ?? 0);
            $old['id_depo']     = (int)($_POST['id_depo'] ?? 0);
            $old['id_rak']      = !empty($_POST['id_rak']) ? (int)$_POST['id_rak'] : null;
            $old['no_batch']    = strtoupper(trim($_POST['no_batch'] ?? ''));
            $old['tgl_masuk']   = trim($_POST['tgl_masuk'] ?? '');
            $old['tgl_expired'] = trim($_POST['tgl_expired'] ?? '');
            $old['jumlah']      = (int)($_POST['jumlah'] ?? 0);

            if ($old['id_produk'] <= 0) $errors[] = 'Produk wajib dipilih.';
            if ($old['id_depo'] <= 0) $errors[] = 'Depo penyimpanan wajib dipilih.';
            if ($old['no_batch'] === '') $errors[] = 'Nomor batch wajib diisi.';
            elseif ($this->batchModel->isBatchExists($old['no_batch'], $old['id_produk'], $old['id_depo'], $id)) {
                $errors[] = 'Nomor batch ' . e($old['no_batch']) . ' sudah digunakan di depo ini.';
            }

            if ($old['jumlah'] <= 0) {
                $errors[] = 'Jumlah karton harus lebih besar dari 0.';
            }

            if ($old['tgl_expired'] <= $old['tgl_masuk']) {
                $errors[] = 'Tanggal kedaluwarsa harus setelah tanggal masuk barang.';
            }

            // Validasi kapasitas rak baru/lama
            if ($old['id_rak']) {
                $rak = $this->rakModel->getById($old['id_rak']);
                if ($rak) {
                    $currentLoad = $this->rakModel->getCurrentLoad($old['id_rak']);
                    // Jika rak sama dengan sebelumnya, kurangi jumlah lama sebelum ditambah jumlah baru
                    if ($batch['id_rak'] == $old['id_rak']) {
                        $currentLoad -= (int)$batch['jumlah'];
                    }
                    if (($currentLoad + $old['jumlah']) > (int)$rak['kapasitas']) {
                        $sisaSlot = max(0, (int)$rak['kapasitas'] - $currentLoad);
                        $errors[] = 'Kapasitas rak ' . e($rak['kode_rak']) . ' tidak mencukupi (sisa slot: ' . $sisaSlot . ' karton).';
                    }
                }
            }

            if (empty($errors)) {
                $this->batchModel->update($id, $old);
                $this->flash('success', 'Batch ' . $old['no_batch'] . ' berhasil diperbarui.');
                $this->redirect(base_url('?page=kedaluwarsa'));
            }
        }

        $this->view('kedaluwarsa/edit', [
            'pageTitle' => 'Edit Batch Stok',
            'batch'     => $batch,
            'produks'   => $produks,
            'depos'     => $depos,
            'allRaks'   => $allRaks,
            'errors'    => $errors,
            'old'       => $old,
        ]);
    }

    /**
     * Proses Hapus Batch (POST + CSRF) (?page=kedaluwarsa&action=delete)
     */
    public function delete(): void {
        AuthGuard::check();
        $this->verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $batch = $this->batchModel->getById($id);

        if (!$batch) {
            $this->flash('error', 'Batch stok tidak ditemukan.');
            $this->redirect(base_url('?page=kedaluwarsa'));
        }

        if ($this->batchModel->isUsedInMutasi($id)) {
            $this->flash('error', 'Gagal menghapus: Batch ' . $batch['no_batch'] . ' sudah tercatat dalam riwayat mutasi antar-depo.');
            $this->redirect(base_url('?page=kedaluwarsa'));
        }

        $this->batchModel->delete($id);
        $this->flash('success', 'Batch ' . $batch['no_batch'] . ' berhasil dihapus.');
        $this->redirect(base_url('?page=kedaluwarsa'));
    }
}

// bagian nurul khoyimah end

