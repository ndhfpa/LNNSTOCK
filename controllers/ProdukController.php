<?php
// bagian lutfi firmansyah start

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Produk.php';

class ProdukController extends Controller {
    private Produk $produkModel;

    public function __construct() {
        $this->produkModel = new Produk();
    }

    /**
     * Halaman Katalog Master Produk FMCG (?page=produk)
     */
    public function index(): void {
        AuthGuard::check();

        $produks = $this->produkModel->getAll();
        $kpi = $this->produkModel->getKpi();

        $this->view('produk/index', [
            'pageTitle' => 'Katalog Master Produk FMCG',
            'produks'   => $produks,
            'kpi'       => $kpi,
        ]);
    }

    /**
     * Formulir dan proses Tambah Produk Baru (?page=produk&action=create)
     */
    public function create(): void {
        AuthGuard::check();

        $errors = [];
        $old = [
            'kode_sku'          => '',
            'nama_produk'       => '',
            'kategori'          => 'Jelly Cups',
            'isi_per_karton'    => 24,
            'masa_simpan_bulan' => 12,
            'buffer_minimum'    => 300,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $old['kode_sku']          = strtoupper(trim($_POST['kode_sku'] ?? ''));
            $old['nama_produk']       = trim($_POST['nama_produk'] ?? '');
            $old['kategori']          = trim($_POST['kategori'] ?? 'Jelly Cups');
            $old['isi_per_karton']    = (int)($_POST['isi_per_karton'] ?? 1);
            $old['masa_simpan_bulan'] = (int)($_POST['masa_simpan_bulan'] ?? 12);
            $old['buffer_minimum']    = (int)($_POST['buffer_minimum'] ?? 0);

            if ($old['kode_sku'] === '') {
                $errors[] = 'Kode SKU wajib diisi.';
            } elseif ($this->produkModel->isSkuExists($old['kode_sku'])) {
                $errors[] = 'Kode SKU ' . e($old['kode_sku']) . ' sudah terdaftar.';
            }

            if ($old['nama_produk'] === '') {
                $errors[] = 'Nama produk wajib diisi.';
            }

            if ($old['isi_per_karton'] <= 0) {
                $errors[] = 'Konversi isi per karton harus minimal 1 pcs.';
            }

            if ($old['buffer_minimum'] < 0) {
                $errors[] = 'Batas buffer minimum tidak boleh negatif.';
            }

            // Validasi & Proses Upload Gambar
            $gambarName = null;
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['gambar'];
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = 'Terjadi kesalahan saat mengunggah berkas gambar.';
                } elseif ($file['size'] > 2 * 1024 * 1024) {
                    $errors[] = 'Ukuran gambar maksimal 2 MB.';
                } else {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime = $finfo->file($file['tmp_name']);
                    $allowedMimes = [
                        'image/jpeg' => 'jpg',
                        'image/png'  => 'png',
                        'image/webp' => 'webp'
                    ];

                    if (!array_key_exists($mime, $allowedMimes)) {
                        $errors[] = 'Format gambar tidak diizinkan. Hanya JPG, PNG, atau WEBP yang diperbolehkan.';
                    } else {
                        $ext = $allowedMimes[$mime];
                        $gambarName = 'prod_' . bin2hex(random_bytes(8)) . '.' . $ext;
                        $targetPath = __DIR__ . '/../public/uploads/produk/' . $gambarName;
                        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                            $errors[] = 'Gagal menyimpan berkas gambar ke direktori upload.';
                            $gambarName = null;
                        }
                    }
                }
            }

            if (empty($errors)) {
                $old['gambar'] = $gambarName;
                $this->produkModel->create($old);
                $this->flash('success', 'Produk ' . $old['nama_produk'] . ' berhasil ditambahkan.');
                $this->redirect(base_url('?page=produk'));
            }
        }

        $this->view('produk/create', [
            'pageTitle' => 'Tambah Produk Baru',
            'errors'    => $errors,
            'old'       => $old,
        ]);
    }

    /**
     * Formulir dan proses Edit Produk (?page=produk&action=edit&id=<id>)
     */
    public function edit(): void {
        AuthGuard::check();

        $id = (int)($_GET['id'] ?? 0);
        $produk = $this->produkModel->getById($id);

        if (!$produk) {
            $this->flash('error', 'Data produk tidak ditemukan.');
            $this->redirect(base_url('?page=produk'));
        }

        $errors = [];
        $old = $produk;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $old['kode_sku']          = strtoupper(trim($_POST['kode_sku'] ?? ''));
            $old['nama_produk']       = trim($_POST['nama_produk'] ?? '');
            $old['kategori']          = trim($_POST['kategori'] ?? '');
            $old['isi_per_karton']    = (int)($_POST['isi_per_karton'] ?? 1);
            $old['masa_simpan_bulan'] = (int)($_POST['masa_simpan_bulan'] ?? 12);
            $old['buffer_minimum']    = (int)($_POST['buffer_minimum'] ?? 0);

            if ($old['kode_sku'] === '') {
                $errors[] = 'Kode SKU wajib diisi.';
            } elseif ($this->produkModel->isSkuExists($old['kode_sku'], $id)) {
                $errors[] = 'Kode SKU ' . e($old['kode_sku']) . ' sudah digunakan oleh produk lain.';
            }

            if ($old['nama_produk'] === '') {
                $errors[] = 'Nama produk wajib diisi.';
            }

            if ($old['isi_per_karton'] <= 0) {
                $errors[] = 'Konversi isi per karton harus minimal 1 pcs.';
            }

            if ($old['buffer_minimum'] < 0) {
                $errors[] = 'Batas buffer minimum tidak boleh negatif.';
            }

            // Cek upload gambar pengganti
            $gambarName = $produk['gambar'];
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['gambar'];
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = 'Terjadi kesalahan saat mengunggah gambar.';
                } elseif ($file['size'] > 2 * 1024 * 1024) {
                    $errors[] = 'Ukuran gambar maksimal 2 MB.';
                } else {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime = $finfo->file($file['tmp_name']);
                    $allowedMimes = [
                        'image/jpeg' => 'jpg',
                        'image/png'  => 'png',
                        'image/webp' => 'webp'
                    ];

                    if (!array_key_exists($mime, $allowedMimes)) {
                        $errors[] = 'Format gambar tidak diizinkan. Hanya JPG, PNG, atau WEBP yang diperbolehkan.';
                    } else {
                        $ext = $allowedMimes[$mime];
                        $newGambarName = 'prod_' . bin2hex(random_bytes(8)) . '.' . $ext;
                        $targetPath = __DIR__ . '/../public/uploads/produk/' . $newGambarName;

                        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                            // Hapus gambar lama jika ada
                            if (!empty($produk['gambar'])) {
                                $oldFilePath = __DIR__ . '/../public/uploads/produk/' . $produk['gambar'];
                                if (file_exists($oldFilePath)) {
                                    @unlink($oldFilePath);
                                }
                            }
                            $gambarName = $newGambarName;
                        } else {
                            $errors[] = 'Gagal menyimpan gambar baru.';
                        }
                    }
                }
            }

            if (empty($errors)) {
                $old['gambar'] = $gambarName;
                $this->produkModel->update($id, $old);
                $this->flash('success', 'Data produk ' . $old['nama_produk'] . ' berhasil diperbarui.');
                $this->redirect(base_url('?page=produk'));
            }
        }

        $this->view('produk/edit', [
            'pageTitle' => 'Edit Master Produk',
            'produk'    => $produk,
            'errors'    => $errors,
            'old'       => $old,
        ]);
    }

    /**
     * Proses Hapus Produk (POST + CSRF) (?page=produk&action=delete)
     */
    public function delete(): void {
        AuthGuard::check();
        $this->verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $produk = $this->produkModel->getById($id);

        if (!$produk) {
            $this->flash('error', 'Produk tidak ditemukan.');
            $this->redirect(base_url('?page=produk'));
        }

        // Cek relasi ke batch stok
        if ($this->produkModel->hasBatches($id)) {
            $this->flash('error', 'Gagal menghapus: Produk "' . $produk['nama_produk'] . '" masih memiliki riwayat batch persediaan.');
            $this->redirect(base_url('?page=produk'));
        }

        // Hapus file gambar jika ada
        if (!empty($produk['gambar'])) {
            $filePath = __DIR__ . '/../public/uploads/produk/' . $produk['gambar'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $this->produkModel->delete($id);
        $this->flash('success', 'Produk ' . $produk['nama_produk'] . ' berhasil dihapus.');
        $this->redirect(base_url('?page=produk'));
    }
}

// bagian lutfi firmansyah end

