<?php
// bagian lutfi firmansyah start

require_once __DIR__ . '/../core/Model.php';

class Produk extends Model {
    /**
     * Mengambil seluruh katalog produk beserta stok fisik dan status buffer
     */
    public function getAll(): array {
        $sql = "SELECT p.*,
                    COALESCE(SUM(b.jumlah), 0) AS stok_karton,
                    COALESCE(SUM(b.jumlah), 0) * p.isi_per_karton AS stok_pcs
                FROM produk p
                LEFT JOIN batch_stok b ON b.id_produk = p.id
                GROUP BY p.id
                ORDER BY p.kode_sku ASC";
        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$row) {
            $stok = (int)$row['stok_karton'];
            $buffer = (int)$row['buffer_minimum'];

            if ($stok < $buffer) {
                $row['status_stok'] = 'Kritis';
                $row['badge_color'] = 'red';
            } elseif ($stok <= round(1.2 * $buffer)) {
                $row['status_stok'] = 'Menipis';
                $row['badge_color'] = 'yellow';
            } else {
                $row['status_stok'] = 'Aman';
                $row['badge_color'] = 'green';
            }
        }

        return $rows;
    }

    /**
     * Mengambil detail produk berdasarkan ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT p.*,
                    COALESCE(SUM(b.jumlah), 0) AS stok_karton,
                    COALESCE(SUM(b.jumlah), 0) * p.isi_per_karton AS stok_pcs
                FROM produk p
                LEFT JOIN batch_stok b ON b.id_produk = p.id
                WHERE p.id = :id
                GROUP BY p.id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Cek apakah kode SKU sudah digunakan (untuk validasi unik)
     */
    public function isSkuExists(string $sku, ?int $exceptId = null): bool {
        $sql = "SELECT id FROM produk WHERE kode_sku = :sku";
        $params = [':sku' => $sku];
        if ($exceptId !== null) {
            $sql .= " AND id != :except_id";
            $params[':except_id'] = $exceptId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    /**
     * Tambah master produk baru
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO produk (kode_sku, nama_produk, kategori, isi_per_karton, masa_simpan_bulan, buffer_minimum, gambar)
                VALUES (:kode_sku, :nama_produk, :kategori, :isi_per_karton, :masa_simpan_bulan, :buffer_minimum, :gambar)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':kode_sku'          => $data['kode_sku'],
            ':nama_produk'       => $data['nama_produk'],
            ':kategori'          => $data['kategori'],
            ':isi_per_karton'    => $data['isi_per_karton'],
            ':masa_simpan_bulan' => $data['masa_simpan_bulan'],
            ':buffer_minimum'    => $data['buffer_minimum'],
            ':gambar'            => $data['gambar'] ?? null,
        ]);
    }

    /**
     * Perbarui data master produk
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE produk 
                SET kode_sku = :kode_sku, nama_produk = :nama_produk, kategori = :kategori,
                    isi_per_karton = :isi_per_karton, masa_simpan_bulan = :masa_simpan_bulan,
                    buffer_minimum = :buffer_minimum, gambar = :gambar
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'                => $id,
            ':kode_sku'          => $data['kode_sku'],
            ':nama_produk'       => $data['nama_produk'],
            ':kategori'          => $data['kategori'],
            ':isi_per_karton'    => $data['isi_per_karton'],
            ':masa_simpan_bulan' => $data['masa_simpan_bulan'],
            ':buffer_minimum'    => $data['buffer_minimum'],
            ':gambar'            => $data['gambar'] ?? null,
        ]);
    }

    /**
     * Hapus produk
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM produk WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Cek apakah produk memiliki riwayat batch
     */
    public function hasBatches(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM batch_stok WHERE id_produk = :id");
        $stmt->execute([':id' => $id]);
        return ((int)($stmt->fetch()['total'] ?? 0)) > 0;
    }

    /**
     * Mengambil ringkasan KPI katalog produk
     */
    public function getKpi(): array {
        $all = $this->getAll();
        $totalSku = count($all);
        $aman = 0;
        $menipis = 0;
        $kritis = 0;

        foreach ($all as $item) {
            if ($item['status_stok'] === 'Aman') $aman++;
            elseif ($item['status_stok'] === 'Menipis') $menipis++;
            elseif ($item['status_stok'] === 'Kritis') $kritis++;
        }

        return [
            'total_sku' => $totalSku,
            'aman'      => $aman,
            'menipis'   => $menipis,
            'kritis'    => $kritis,
        ];
    }
}

// bagian lutfi firmansyah end

