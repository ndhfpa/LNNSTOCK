<?php
// bagian nurul khoyimah start

require_once __DIR__ . '/../core/Model.php';

class Mutasi extends Model {
    /**
     * Mengambil daftar riwayat mutasi antar-depo
     */
    public function getAll(?string $status = null): array {
        $sql = "SELECT m.*,
                    b.no_batch, b.tgl_expired, b.id_produk,
                    p.nama_produk, p.kode_sku,
                    d_asal.id AS id_depo_asal, d_asal.kode_depo AS kode_asal, d_asal.nama_depo AS nama_asal,
                    d_tujuan.kode_depo AS kode_tujuan, d_tujuan.nama_depo AS nama_tujuan,
                    u.nama_lengkap AS pembuat
                FROM mutasi m
                JOIN batch_stok b ON b.id = m.id_batch
                JOIN produk p ON p.id = b.id_produk
                JOIN depo d_asal ON d_asal.id = b.id_depo
                JOIN depo d_tujuan ON d_tujuan.id = m.id_depo_tujuan
                JOIN users u ON u.id = m.id_user";

        $params = [];
        if ($status && $status !== 'Semua') {
            $sql .= " WHERE m.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY m.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil detail satu mutasi berdasarkan ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT m.*,
                    b.no_batch, b.tgl_expired, b.jumlah AS stok_batch_asal, b.id_produk,
                    p.nama_produk, p.kode_sku,
                    d_asal.id AS id_depo_asal, d_asal.kode_depo AS kode_asal, d_asal.nama_depo AS nama_asal,
                    d_tujuan.kode_depo AS kode_tujuan, d_tujuan.nama_depo AS nama_tujuan,
                    u.nama_lengkap AS pembuat
                FROM mutasi m
                JOIN batch_stok b ON b.id = m.id_batch
                JOIN produk p ON p.id = b.id_produk
                JOIN depo d_asal ON d_asal.id = b.id_depo
                JOIN depo d_tujuan ON d_tujuan.id = m.id_depo_tujuan
                JOIN users u ON u.id = m.id_user
                WHERE m.id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Generate nomor mutasi otomatis berurutan (MT-YYYYMM-XXX)
     */
    public function generateNoMutasi(): string {
        $prefix = 'MT-' . date('Ym') . '-';
        $stmt = $this->db->prepare("SELECT no_mutasi FROM mutasi WHERE no_mutasi LIKE :prefix ORDER BY id DESC LIMIT 1");
        $stmt->execute([':prefix' => $prefix . '%']);
        $last = $stmt->fetch();

        if ($last) {
            $lastNum = (int)substr($last['no_mutasi'], -3);
            $nextNum = str_pad((string)($lastNum + 1), 3, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '001';
        }

        return $prefix . $nextNum;
    }

    /**
     * Tambah data mutasi baru berstatus Menunggu
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO mutasi (no_mutasi, id_batch, id_depo_tujuan, jumlah, tanggal, status, id_user)
                VALUES (:no_mutasi, :id_batch, :id_depo_tujuan, :jumlah, :tanggal, 'Menunggu', :id_user)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':no_mutasi'       => $data['no_mutasi'],
            ':id_batch'        => $data['id_batch'],
            ':id_depo_tujuan'  => $data['id_depo_tujuan'],
            ':jumlah'          => $data['jumlah'],
            ':tanggal'         => $data['tanggal'],
            ':id_user'         => $data['id_user'],
        ]);
    }

    /**
     * Perbarui data mutasi (hanya berlaku jika status Menunggu)
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE mutasi 
                SET id_batch = :id_batch, id_depo_tujuan = :id_depo_tujuan, 
                    jumlah = :jumlah, tanggal = :tanggal 
                WHERE id = :id AND status = 'Menunggu'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'             => $id,
            ':id_batch'       => $data['id_batch'],
            ':id_depo_tujuan' => $data['id_depo_tujuan'],
            ':jumlah'         => $data['jumlah'],
            ':tanggal'        => $data['tanggal'],
        ]);
    }

    /**
     * Batalkan mutasi (ubah status dari Menunggu ke Dibatalkan)
     */
    public function cancel(int $id): bool {
        $stmt = $this->db->prepare("UPDATE mutasi SET status = 'Dibatalkan' WHERE id = :id AND status = 'Menunggu'");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Hapus mutasi (hanya boleh jika status Menunggu atau Dibatalkan)
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM mutasi WHERE id = :id AND status IN ('Menunggu', 'Dibatalkan')");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Selesaikan Mutasi Stok (Menggunakan Transaksi Database ACID dengan SELECT ... FOR UPDATE)
     * Mengurangi batch asal dan memindahkan/menambah ke depo tujuan
     */
    public function complete(int $id): array {
        try {
            $this->db->beginTransaction();

            // 1. Ambil dan kunci data mutasi
            $stmtMutasi = $this->db->prepare("SELECT * FROM mutasi WHERE id = :id FOR UPDATE");
            $stmtMutasi->execute([':id' => $id]);
            $mutasi = $stmtMutasi->fetch();

            if (!$mutasi) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Data mutasi tidak ditemukan.'];
            }

            if ($mutasi['status'] !== 'Menunggu') {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Hanya mutasi berstatus Menunggu yang dapat diselesaikan.'];
            }

            $idBatchAsal = (int)$mutasi['id_batch'];
            $idDepoTujuan = (int)$mutasi['id_depo_tujuan'];
            $jumlahTransfer = (int)$mutasi['jumlah'];

            // 2. Kunci batch asal dan cek stok
            $stmtBatchAsal = $this->db->prepare("SELECT * FROM batch_stok WHERE id = :id FOR UPDATE");
            $stmtBatchAsal->execute([':id' => $idBatchAsal]);
            $batchAsal = $stmtBatchAsal->fetch();

            if (!$batchAsal) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Batch asal tidak ditemukan.'];
            }

            if ($batchAsal['id_depo'] == $idDepoTujuan) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Depo tujuan tidak boleh sama dengan depo asal.'];
            }

            if ($jumlahTransfer <= 0 || $jumlahTransfer > (int)$batchAsal['jumlah']) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Jumlah mutasi melebihi sisa stok fisik batch asal saat ini (' . $batchAsal['jumlah'] . ' karton).'];
            }

            // 3. Kurangi stok pada batch asal
            $stmtKurang = $this->db->prepare("UPDATE batch_stok SET jumlah = jumlah - :qty WHERE id = :id");
            $stmtKurang->execute([
                ':qty' => $jumlahTransfer,
                ':id'  => $idBatchAsal,
            ]);

            // 4. Cari apakah batch bernomor sama dan produk sama sudah ada di depo tujuan
            $stmtCekTujuan = $this->db->prepare("SELECT id FROM batch_stok 
                                                WHERE no_batch = :no_batch 
                                                  AND id_produk = :id_produk 
                                                  AND id_depo = :id_depo 
                                                LIMIT 1 FOR UPDATE");
            $stmtCekTujuan->execute([
                ':no_batch'  => $batchAsal['no_batch'],
                ':id_produk' => $batchAsal['id_produk'],
                ':id_depo'   => $idDepoTujuan,
            ]);
            $batchTujuan = $stmtCekTujuan->fetch();

            if ($batchTujuan) {
                // Tambah jumlah ke batch yang sudah ada
                $stmtTambah = $this->db->prepare("UPDATE batch_stok SET jumlah = jumlah + :qty WHERE id = :id");
                $stmtTambah->execute([
                    ':qty' => $jumlahTransfer,
                    ':id'  => $batchTujuan['id'],
                ]);
            } else {
                // Buat baris batch baru di depo tujuan dengan id_rak NULL
                $stmtInsertBatch = $this->db->prepare("INSERT INTO batch_stok (id_produk, id_depo, id_rak, no_batch, tgl_masuk, tgl_expired, jumlah)
                                                      VALUES (:id_produk, :id_depo, NULL, :no_batch, CURDATE(), :tgl_expired, :jumlah)");
                $stmtInsertBatch->execute([
                    ':id_produk'   => $batchAsal['id_produk'],
                    ':id_depo'     => $idDepoTujuan,
                    ':no_batch'    => $batchAsal['no_batch'],
                    ':tgl_expired' => $batchAsal['tgl_expired'],
                    ':jumlah'      => $jumlahTransfer,
                ]);
            }

            // 5. Ubah status mutasi menjadi Selesai
            $stmtUpdateMutasi = $this->db->prepare("UPDATE mutasi SET status = 'Selesai' WHERE id = :id");
            $stmtUpdateMutasi->execute([':id' => $id]);

            // Commit transaksi jika seluruh tahapan sukses
            $this->db->commit();
            return ['success' => true, 'message' => 'Mutasi stok ' . $mutasi['no_mutasi'] . ' berhasil disetujui & diselesaikan. Stok otomatis diperbarui.'];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Mutasi Complete Transaction Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem saat memproses transaksi mutasi: ' . $e->getMessage()];
        }
    }

    /**
     * Mengambil ringkasan KPI modul mutasi
     */
    public function getKpi(): array {
        $stmt = $this->db->query("SELECT 
            SUM(CASE WHEN status = 'Menunggu' THEN 1 ELSE 0 END) AS pending_count,
            SUM(CASE WHEN status = 'Selesai' AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) THEN 1 ELSE 0 END) AS selesai_bulan_ini,
            COALESCE(SUM(CASE WHEN status = 'Selesai' AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) THEN jumlah ELSE 0 END), 0) AS karton_dipindahkan,
            COUNT(*) AS total_semua,
            SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) AS total_selesai,
            SUM(CASE WHEN status = 'Dibatalkan' THEN 1 ELSE 0 END) AS total_dibatalkan
            FROM mutasi");
        $res = $stmt->fetch();

        return [
            'pending_count'      => (int)($res['pending_count'] ?? 0),
            'selesai_bulan_ini'  => (int)($res['selesai_bulan_ini'] ?? 0),
            'karton_dipindahkan' => (int)($res['karton_dipindahkan'] ?? 0),
            'total_semua'        => (int)($res['total_semua'] ?? 0),
            'total_selesai'      => (int)($res['total_selesai'] ?? 0),
            'total_dibatalkan'   => (int)($res['total_dibatalkan'] ?? 0),
        ];
    }
}

// bagian nurul khoyimah end

