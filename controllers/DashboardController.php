<?php
// bagian bersama start

require_once __DIR__ . '/../core/Controller.php';

class DashboardController extends Controller {
    /**
     * Halaman Dashboard Utama LNNSTOCK (?page=dashboard)
     */
    public function index(): void {
        AuthGuard::check();
        $db = Database::connect();

        // 1. Sapaan Berdasarkan Jam Lokal
        $hour = (int)date('G');
        if ($hour >= 4 && $hour < 11) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 19) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        // 2. 4 Kartu KPI Utama Real-time dari Database
        // KPI 1: Depo & Total Kapasitas
        $stmtDepo = $db->query("SELECT 
            COUNT(DISTINCT d.id) AS total_depo,
            COALESCE(SUM(r.kapasitas), 0) AS total_kapasitas,
            (SELECT COALESCE(SUM(jumlah), 0) FROM batch_stok) AS total_stok
            FROM depo d
            LEFT JOIN rak r ON r.id_depo = d.id");
        $depoData = $stmtDepo->fetch();

        $totalDepo = (int)($depoData['total_depo'] ?? 0);
        $totalKapasitas = (int)($depoData['total_kapasitas'] ?? 0);
        $totalStok = (int)($depoData['total_stok'] ?? 0);
        $okupansiGlobal = $totalKapasitas > 0 ? round(($totalStok / $totalKapasitas) * 100, 1) : 0;

        // KPI 2: Total Varian FMCG
        $stmtProduk = $db->query("SELECT COUNT(*) AS total_sku FROM produk");
        $totalSku = (int)($stmtProduk->fetch()['total_sku'] ?? 0);

        // KPI 3: Mutasi Bulan Berjalan
        $stmtMutasi = $db->query("SELECT 
            COUNT(*) AS total_mutasi,
            COALESCE(SUM(CASE WHEN status = 'Selesai' THEN jumlah ELSE 0 END), 0) AS karton_dipindahkan
            FROM mutasi 
            WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())");
        $mutasiData = $stmtMutasi->fetch();
        $mutasiBulanIni = (int)($mutasiData['total_mutasi'] ?? 0);
        $kartonMutasi = (int)($mutasiData['karton_dipindahkan'] ?? 0);

        // 3. Panel: Peringatan Kedaluwarsa Dini (Top 2 Batch Terkritis)
        $stmtUrgent = $db->query("SELECT b.*, p.nama_produk, p.kode_sku, d.kode_depo, d.nama_depo,
            DATEDIFF(b.tgl_expired, CURDATE()) AS sisa_hari
            FROM batch_stok b
            JOIN produk p ON p.id = b.id_produk
            JOIN depo d ON d.id = b.id_depo
            WHERE b.jumlah > 0
            ORDER BY b.tgl_expired ASC
            LIMIT 2");
        $urgentBatches = $stmtUrgent->fetchAll();

        // 4. Panel: Stok di Bawah Batas Minimum (Top 2 Produk Kritis)
        $stmtKritisProd = $db->query("SELECT p.*,
            COALESCE(SUM(b.jumlah), 0) AS stok_karton
            FROM produk p
            LEFT JOIN batch_stok b ON b.id_produk = p.id
            GROUP BY p.id
            HAVING stok_karton < p.buffer_minimum
            ORDER BY (p.buffer_minimum - COALESCE(SUM(b.jumlah), 0)) DESC
            LIMIT 2");
        $kritisProduk = $stmtKritisProd->fetchAll();

        // 5. Panel: Denah Rak & Okupansi Ringkas per Depo
        $stmtDepoSummary = $db->query("SELECT d.id, d.kode_depo, d.nama_depo,
            COALESCE(SUM(r.kapasitas), 0) AS kapasitas,
            (SELECT COALESCE(SUM(b.jumlah), 0) FROM batch_stok b WHERE b.id_depo = d.id) AS terisi
            FROM depo d
            LEFT JOIN rak r ON r.id_depo = d.id
            GROUP BY d.id
            ORDER BY d.kode_depo ASC");
        $depoSummaries = $stmtDepoSummary->fetchAll();

        // 6. Tabel: Transaksi Mutasi Terkini (5 Transaksi Terakhir)
        $stmtRecentMutasi = $db->query("SELECT m.*,
            b.no_batch, p.nama_produk,
            d_asal.nama_depo AS depo_asal, d_tujuan.nama_depo AS depo_tujuan
            FROM mutasi m
            JOIN batch_stok b ON b.id = m.id_batch
            JOIN produk p ON p.id = b.id_produk
            JOIN depo d_asal ON d_asal.id = b.id_depo
            JOIN depo d_tujuan ON d_tujuan.id = m.id_depo_tujuan
            ORDER BY m.id DESC
            LIMIT 5");
        $recentMutasi = $stmtRecentMutasi->fetchAll();

        $this->view('dashboard/index', [
            'pageTitle'       => 'Dashboard Logistik',
            'greeting'        => $greeting,
            'totalDepo'       => $totalDepo,
            'totalKapasitas'  => $totalKapasitas,
            'totalStok'       => $totalStok,
            'okupansiGlobal'  => $okupansiGlobal,
            'totalSku'        => $totalSku,
            'mutasiBulanIni'  => $mutasiBulanIni,
            'kartonMutasi'    => $kartonMutasi,
            'urgentBatches'   => $urgentBatches,
            'kritisProduk'    => $kritisProduk,
            'depoSummaries'   => $depoSummaries,
            'recentMutasi'    => $recentMutasi,
        ]);
    }
}

// bagian bersama end
