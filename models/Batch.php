<?php
// bagian nurul khoyimah start

require_once __DIR__ . '/../core/Model.php';

class Batch extends Model {
    /**
     * Mengambil daftar batch stok berdasarkan FEFO (First Expired First Out)
     */
    public function getAll(?int $depoId = null, ?string $statusFilter = null): array {
        $sql = "SELECT b.*, 
                    p.nama_produk, p.kode_sku, p.kategori,
                    d.nama_depo, d.kode_depo,
                    r.kode_rak, r.zona,
                    DATEDIFF(b.tgl_expired, CURDATE()) AS sisa_hari
                FROM batch_stok b
                JOIN produk p ON p.id = b.id_produk
                JOIN depo d ON d.id = b.id_depo
                LEFT JOIN rak r ON r.id = b.id_rak
                WHERE b.jumlah > 0";
        
        $params = [];

        if ($depoId !== null && $depoId > 0) {
            $sql .= " AND b.id_depo = :depo_id";
            $params[':depo_id'] = $depoId;
        }

        $sql .= " ORDER BY b.tgl_expired ASC, b.tgl_masuk ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $sisa = (int)$row['sisa_hari'];

            if ($sisa < 0) {
                $row['status_expired'] = 'Sudah Kedaluwarsa';
                $row['badge_color'] = 'red';
            } elseif ($sisa <= 30) {
                $row['status_expired'] = 'Kritis';
                $row['badge_color'] = 'red';
            } elseif ($sisa <= 90) {
                $row['status_expired'] = 'Waspada';
                $row['badge_color'] = 'yellow';
            } else {
                $row['status_expired'] = 'Aman';
                $row['badge_color'] = 'green';
            }

            if ($statusFilter && $statusFilter !== 'Semua') {
                if ($statusFilter === 'Kritis' && ($sisa > 30)) continue;
                if ($statusFilter === 'Waspada' && ($sisa <= 30 || $sisa > 90)) continue;
                if ($statusFilter === 'Aman' && ($sisa <= 90)) continue;
            }

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Mengambil detail satu batch berdasarkan ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT b.*, 
                    p.nama_produk, p.kode_sku, p.masa_simpan_bulan,
                    d.nama_depo, d.kode_depo,
                    r.kode_rak, r.kapasitas,
                    DATEDIFF(b.tgl_expired, CURDATE()) AS sisa_hari
                FROM batch_stok b
                JOIN produk p ON p.id = b.id_produk
                JOIN depo d ON d.id = b.id_depo
                LEFT JOIN rak r ON r.id = b.id_rak
                WHERE b.id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Cek apakah no batch sudah ada pada produk dan depo yang sama
     */
    public function isBatchExists(string $noBatch, int $idProduk, int $idDepo, ?int $exceptId = null): bool {
        $sql = "SELECT id FROM batch_stok 
                WHERE no_batch = :no_batch AND id_produk = :id_produk AND id_depo = :id_depo";
        $params = [
            ':no_batch'  => $noBatch,
            ':id_produk' => $idProduk,
            ':id_depo'   => $idDepo,
        ];

        if ($exceptId !== null) {
            $sql .= " AND id != :except_id";
            $params[':except_id'] = $exceptId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    /**
     * Tambah data batch stok baru
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO batch_stok (id_produk, id_depo, id_rak, no_batch, tgl_masuk, tgl_expired, jumlah)
                VALUES (:id_produk, :id_depo, :id_rak, :no_batch, :tgl_masuk, :tgl_expired, :jumlah)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_produk'   => $data['id_produk'],
            ':id_depo'     => $data['id_depo'],
            ':id_rak'      => !empty($data['id_rak']) ? $data['id_rak'] : null,
            ':no_batch'    => $data['no_batch'],
            ':tgl_masuk'   => $data['tgl_masuk'],
            ':tgl_expired' => $data['tgl_expired'],
            ':jumlah'      => $data['jumlah'],
        ]);
    }

    /**
     * Perbarui data batch stok
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE batch_stok 
                SET id_produk = :id_produk, id_depo = :id_depo, id_rak = :id_rak,
                    no_batch = :no_batch, tgl_masuk = :tgl_masuk, tgl_expired = :tgl_expired,
                    jumlah = :jumlah
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'          => $id,
            ':id_produk'   => $data['id_produk'],
            ':id_depo'     => $data['id_depo'],
            ':id_rak'      => !empty($data['id_rak']) ? $data['id_rak'] : null,
            ':no_batch'    => $data['no_batch'],
            ':tgl_masuk'   => $data['tgl_masuk'],
            ':tgl_expired' => $data['tgl_expired'],
            ':jumlah'      => $data['jumlah'],
        ]);
    }

    /**
     * Hapus batch stok
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM batch_stok WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Periksa apakah batch pernah digunakan di tabel mutasi
     */
    public function isUsedInMutasi(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM mutasi WHERE id_batch = :id");
        $stmt->execute([':id' => $id]);
        return ((int)($stmt->fetch()['total'] ?? 0)) > 0;
    }

    /**
     * Mengambil 1 batch paling urgent/kritis untuk banner peringatan
     */
    public function getMostUrgentBatch(): ?array {
        $sql = "SELECT b.*, p.nama_produk, p.kode_sku, d.nama_depo,
                    DATEDIFF(b.tgl_expired, CURDATE()) AS sisa_hari
                FROM batch_stok b
                JOIN produk p ON p.id = b.id_produk
                JOIN depo d ON d.id = b.id_depo
                WHERE b.jumlah > 0 AND DATEDIFF(b.tgl_expired, CURDATE()) <= 30
                ORDER BY b.tgl_expired ASC
                LIMIT 1";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Mengambil ringkasan KPI monitoring kedaluwarsa
     */
    public function getKpi(): array {
        $sql = "SELECT 
                    COUNT(*) AS total_batch,
                    COALESCE(SUM(jumlah), 0) AS total_stok,
                    SUM(CASE WHEN DATEDIFF(tgl_expired, CURDATE()) > 90 THEN 1 ELSE 0 END) AS batch_aman,
                    SUM(CASE WHEN DATEDIFF(tgl_expired, CURDATE()) > 90 THEN jumlah ELSE 0 END) AS stok_aman,
                    SUM(CASE WHEN DATEDIFF(tgl_expired, CURDATE()) BETWEEN 31 AND 90 THEN 1 ELSE 0 END) AS batch_waspada,
                    SUM(CASE WHEN DATEDIFF(tgl_expired, CURDATE()) BETWEEN 31 AND 90 THEN jumlah ELSE 0 END) AS stok_waspada,
                    SUM(CASE WHEN DATEDIFF(tgl_expired, CURDATE()) <= 30 THEN 1 ELSE 0 END) AS batch_kritis,
                    SUM(CASE WHEN DATEDIFF(tgl_expired, CURDATE()) <= 30 THEN jumlah ELSE 0 END) AS stok_kritis
                FROM batch_stok
                WHERE jumlah > 0";
        $stmt = $this->db->query($sql);
        $res = $stmt->fetch();

        $totalStok = (int)($res['total_stok'] ?? 0);

        return [
            'total_batch'   => (int)($res['total_batch'] ?? 0),
            'total_stok'    => $totalStok,
            'batch_aman'    => (int)($res['batch_aman'] ?? 0),
            'stok_aman'     => (int)($res['stok_aman'] ?? 0),
            'pct_aman'      => $totalStok > 0 ? round(((int)$res['stok_aman'] / $totalStok) * 100, 1) : 0,
            'batch_waspada' => (int)($res['batch_waspada'] ?? 0),
            'stok_waspada'  => (int)($res['stok_waspada'] ?? 0),
            'pct_waspada'   => $totalStok > 0 ? round(((int)$res['stok_waspada'] / $totalStok) * 100, 1) : 0,
            'batch_kritis'  => (int)($res['batch_kritis'] ?? 0),
            'stok_kritis'   => (int)($res['stok_kritis'] ?? 0),
            'pct_kritis'    => $totalStok > 0 ? round(((int)$res['stok_kritis'] / $totalStok) * 100, 1) : 0,
        ];
    }

    /**
     * Mengambil daftar batch untuk opsi form Mutasi Antar-Depo (urut FEFO)
     */
    public function getBatchesForMutasi(): array {
        $sql = "SELECT b.id, b.no_batch, b.jumlah AS stok_tersedia, b.tgl_expired,
                    b.id_depo, d.nama_depo, d.kode_depo,
                    p.nama_produk, p.kode_sku,
                    DATEDIFF(b.tgl_expired, CURDATE()) AS sisa_hari
                FROM batch_stok b
                JOIN produk p ON p.id = b.id_produk
                JOIN depo d ON d.id = b.id_depo
                WHERE b.jumlah > 0
                ORDER BY b.tgl_expired ASC, b.jumlah DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}

// bagian nurul khoyimah end

