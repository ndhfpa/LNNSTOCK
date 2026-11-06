<?php
// bagian nadhifatul hilmy ns start

require_once __DIR__ . '/../core/Model.php';

class Depo extends Model {
    /**
     * Mengambil semua data depo beserta kalkulasi kapasitas dan stok terisi
     */
    public function getAll(): array {
        $sql = "SELECT d.*, 
                    COALESCE(SUM(r.kapasitas), 0) AS total_kapasitas,
                    (SELECT COALESCE(SUM(b.jumlah), 0) FROM batch_stok b WHERE b.id_depo = d.id) AS total_terisi
                FROM depo d
                LEFT JOIN rak r ON r.id_depo = d.id
                GROUP BY d.id
                ORDER BY d.kode_depo ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil detail satu depo berdasarkan ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT d.*, 
                    COALESCE(SUM(r.kapasitas), 0) AS total_kapasitas,
                    (SELECT COALESCE(SUM(b.jumlah), 0) FROM batch_stok b WHERE b.id_depo = d.id) AS total_terisi
                FROM depo d
                LEFT JOIN rak r ON r.id_depo = d.id
                WHERE d.id = :id
                GROUP BY d.id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Cek apakah kode depo sudah digunakan (untuk validasi unik)
     */
    public function isKodeExists(string $kode, ?int $exceptId = null): bool {
        $sql = "SELECT id FROM depo WHERE kode_depo = :kode";
        $params = [':kode' => $kode];
        if ($exceptId !== null) {
            $sql .= " AND id != :except_id";
            $params[':except_id'] = $exceptId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    /**
     * Tambah data depo baru
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO depo (kode_depo, nama_depo, jenis_depo, lokasi, status) 
                VALUES (:kode_depo, :nama_depo, :jenis_depo, :lokasi, :status)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':kode_depo'  => $data['kode_depo'],
            ':nama_depo'  => $data['nama_depo'],
            ':jenis_depo' => $data['jenis_depo'],
            ':lokasi'     => $data['lokasi'],
            ':status'     => $data['status'] ?? 'Aktif',
        ]);
    }

    /**
     * Perbarui data depo
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE depo 
                SET kode_depo = :kode_depo, nama_depo = :nama_depo, jenis_depo = :jenis_depo, 
                    lokasi = :lokasi, status = :status 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'         => $id,
            ':kode_depo'  => $data['kode_depo'],
            ':nama_depo'  => $data['nama_depo'],
            ':jenis_depo' => $data['jenis_depo'],
            ':lokasi'     => $data['lokasi'],
            ':status'     => $data['status'] ?? 'Aktif',
        ]);
    }

    /**
     * Hapus depo
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM depo WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Mengambil ringkasan KPI untuk modul depo
     */
    public function getKpi(): array {
        $stmt = $this->db->query("SELECT 
            COUNT(*) AS total_depo,
            SUM(CASE WHEN status = 'Aktif' THEN 1 ELSE 0 END) AS total_aktif
            FROM depo");
        $res = $stmt->fetch();

        $stmtKap = $this->db->query("SELECT COALESCE(SUM(kapasitas), 0) AS total_kapasitas FROM rak");
        $resKap = $stmtKap->fetch();

        return [
            'total_depo'      => (int)($res['total_depo'] ?? 0),
            'depo_aktif'      => (int)($res['total_aktif'] ?? 0),
            'total_kapasitas' => (int)($resKap['total_kapasitas'] ?? 0),
        ];
    }
}

// bagian nadhifatul hilmy ns end

