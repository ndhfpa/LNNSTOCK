<?php
// bagian nadhifatul hilmy ns start

require_once __DIR__ . '/../core/Model.php';

class Rak extends Model {
    /**
     * Mengambil daftar rak berdasarkan ID Depo beserta okupansi dan produk dominan
     */
    public function getByDepo(int $depoId, ?string $zona = null): array {
        $sql = "SELECT r.*,
                    COALESCE(SUM(b.jumlah), 0) AS terisi,
                    (SELECT p.nama_produk 
                     FROM batch_stok b2 
                     JOIN produk p ON p.id = b2.id_produk 
                     WHERE b2.id_rak = r.id AND b2.jumlah > 0 
                     ORDER BY b2.jumlah DESC LIMIT 1) AS produk_dominan,
                    (SELECT b2.no_batch 
                     FROM batch_stok b2 
                     WHERE b2.id_rak = r.id AND b2.jumlah > 0 
                     ORDER BY b2.jumlah DESC LIMIT 1) AS batch_dominan
                FROM rak r
                LEFT JOIN batch_stok b ON b.id_rak = r.id
                WHERE r.id_depo = :id_depo";
        
        $params = [':id_depo' => $depoId];

        if ($zona && $zona !== 'Semua') {
            $sql .= " AND r.zona = :zona";
            $params[':zona'] = $zona;
        }

        $sql .= " GROUP BY r.id ORDER BY r.zona ASC, r.kode_rak ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Hitung persentase okupansi dan status warna
        foreach ($rows as &$row) {
            $kapasitas = (int)$row['kapasitas'];
            $terisi = (int)$row['terisi'];
            $row['persen_okupansi'] = ($kapasitas > 0) ? round(($terisi / $kapasitas) * 100) : 0;

            if ($row['status_rak'] === 'Maintenance') {
                $row['status_warna'] = 'gray';
                $row['status_label'] = 'Maintenance';
            } elseif ($row['persen_okupansi'] > 90) {
                $row['status_warna'] = 'red';
                $row['status_label'] = 'Penuh';
            } elseif ($row['persen_okupansi'] >= 60) {
                $row['status_warna'] = 'yellow';
                $row['status_label'] = 'Terisi Optimal';
            } else {
                $row['status_warna'] = 'green';
                $row['status_label'] = 'Tersedia';
            }
        }

        return $rows;
    }

    /**
     * Ambil data rak berdasarkan ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT r.*, d.nama_depo, d.kode_depo,
                    COALESCE(SUM(b.jumlah), 0) AS terisi
                FROM rak r
                JOIN depo d ON d.id = r.id_depo
                LEFT JOIN batch_stok b ON b.id_rak = r.id
                WHERE r.id = :id
                GROUP BY r.id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Mengambil daftar batch barang yang tersimpan di dalam rak tertentu
     */
    public function getBatchesInRak(int $rakId): array {
        $sql = "SELECT b.*, p.nama_produk, p.kode_sku,
                    DATEDIFF(b.tgl_expired, CURDATE()) AS sisa_hari
                FROM batch_stok b
                JOIN produk p ON p.id = b.id_produk
                WHERE b.id_rak = :rak_id AND b.jumlah > 0
                ORDER BY b.tgl_expired ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':rak_id' => $rakId]);
        return $stmt->fetchAll();
    }

    /**
     * Hitung total karton pada depo yang belum ditempatkan di rak (id_rak IS NULL)
     */
    public function getUnassignedStock(int $depoId): int {
        $sql = "SELECT COALESCE(SUM(jumlah), 0) AS total 
                FROM batch_stok 
                WHERE id_depo = :depo_id AND id_rak IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':depo_id' => $depoId]);
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    /**
     * Cek apakah kode rak sudah ada pada depo yang sama (unique key uq_rak)
     */
    public function isKodeExistsInDepo(int $depoId, string $kodeRak, ?int $exceptId = null): bool {
        $sql = "SELECT id FROM rak WHERE id_depo = :depo_id AND kode_rak = :kode_rak";
        $params = [
            ':depo_id'  => $depoId,
            ':kode_rak' => $kodeRak
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
     * Tambah rak baru
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO rak (id_depo, kode_rak, zona, kapasitas, status_rak)
                VALUES (:id_depo, :kode_rak, :zona, :kapasitas, :status_rak)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_depo'    => $data['id_depo'],
            ':kode_rak'   => $data['kode_rak'],
            ':zona'       => $data['zona'],
            ':kapasitas'  => $data['kapasitas'],
            ':status_rak' => $data['status_rak'] ?? 'Aktif',
        ]);
    }

    /**
     * Perbarui data rak
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE rak 
                SET kode_rak = :kode_rak, zona = :zona, kapasitas = :kapasitas, status_rak = :status_rak 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'         => $id,
            ':kode_rak'   => $data['kode_rak'],
            ':zona'       => $data['zona'],
            ':kapasitas'  => $data['kapasitas'],
            ':status_rak' => $data['status_rak'] ?? 'Aktif',
        ]);
    }

    /**
     * Hapus rak
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM rak WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Ambil jumlah stok tersimpan saat ini di rak
     */
    public function getCurrentLoad(int $rakId): int {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(jumlah), 0) AS total FROM batch_stok WHERE id_rak = :id");
        $stmt->execute([':id' => $rakId]);
        return (int)($stmt->fetch()['total'] ?? 0);
    }
}

// bagian nadhifatul hilmy ns end

