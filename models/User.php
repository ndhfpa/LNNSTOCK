<?php
// bagian bersama start

require_once __DIR__ . '/../core/Model.php';

class User extends Model {
    /**
     * Ambil seluruh data pengguna sistem
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT id, username, nama_lengkap, role FROM users ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Cari pengguna berdasarkan username
     */
    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Cari pengguna berdasarkan ID (termasuk hash password untuk verifikasi)
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, username, nama_lengkap, role, password FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Cek apakah username sudah ada (untuk validasi unik)
     */
    public function isUsernameExists(string $username, ?int $exceptId = null): bool {
        $sql = "SELECT id FROM users WHERE username = :username";
        $params = [':username' => $username];
        if ($exceptId !== null) {
            $sql .= " AND id != :except_id";
            $params[':except_id'] = $exceptId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    /**
     * Tambah pengguna baru
     * @return int|bool ID pengguna baru jika berhasil, false jika gagal
     */
    public function create(array $data): int|bool {
        $sql = "INSERT INTO users (username, nama_lengkap, role, password) 
                VALUES (:username, :nama_lengkap, :role, :password)";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([
            ':username'     => $data['username'],
            ':nama_lengkap' => $data['nama_lengkap'],
            ':role'         => $data['role'],
            ':password'     => $data['password'],
        ]);
        return $res ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * Perbarui data pengguna (nama lengkap, role, dan password opsional)
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['username'])) {
            $fields[] = "username = :username";
            $params[':username'] = $data['username'];
        }
        if (isset($data['nama_lengkap'])) {
            $fields[] = "nama_lengkap = :nama_lengkap";
            $params[':nama_lengkap'] = $data['nama_lengkap'];
        }
        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params[':role'] = $data['role'];
        }
        if (!empty($data['password'])) {
            $fields[] = "password = :password";
            $params[':password'] = $data['password'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Perbarui nama lengkap profil mandiri
     */
    public function updateProfile(int $id, string $namaLengkap): bool {
        $stmt = $this->db->prepare("UPDATE users SET nama_lengkap = :nama_lengkap WHERE id = :id");
        return $stmt->execute([
            ':id'           => $id,
            ':nama_lengkap' => $namaLengkap,
        ]);
    }

    /**
     * Perbarui kata sandi profil mandiri
     */
    public function updatePassword(int $id, string $hashedPassword): bool {
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([
            ':id'       => $id,
            ':password' => $hashedPassword,
        ]);
    }

    /**
     * Hitung jumlah pengguna berdasarkan role
     */
    public function countByRole(string $role): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM users WHERE role = :role");
        $stmt->execute([':role' => $role]);
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    /**
     * Cek apakah pengguna sudah memiliki riwayat mutasi
     */
    public function hasMutasi(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM mutasi WHERE id_user = :id");
        $stmt->execute([':id' => $id]);
        return (int)($stmt->fetch()['total'] ?? 0) > 0;
    }

    /**
     * Hapus data pengguna
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}

// bagian bersama end
