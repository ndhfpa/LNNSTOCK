-- ========================================================
-- DATABASE LNNSTOCK - WMS PT RIYANA CIPTA PANGAN INDONESIA
-- Proyek Akhir Pemrograman Web Dasar - STMIK IKMI Cirebon
-- Tagline: Manage Smarter, Move Better.
-- ========================================================

CREATE DATABASE IF NOT EXISTS db_lnnstock CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_lnnstock;

-- bagian bersama start
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  nama_lengkap VARCHAR(100) NOT NULL,
  role VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL          -- hash BCRYPT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- bagian bersama end

-- bagian nadhifatul hilmy ns start
CREATE TABLE IF NOT EXISTS depo (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_depo VARCHAR(10) NOT NULL UNIQUE,  -- GD001
  nama_depo VARCHAR(100) NOT NULL,
  jenis_depo VARCHAR(30) NOT NULL,        -- Bahan Baku / Bahan Kemas / Produk Jadi / Distribusi
  lokasi VARCHAR(150) NOT NULL,
  status VARCHAR(10) NOT NULL DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS rak (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_depo INT NOT NULL,
  kode_rak VARCHAR(10) NOT NULL,          -- A-01
  zona VARCHAR(5) NOT NULL,               -- A / B / C
  kapasitas INT NOT NULL,                 -- karton
  status_rak VARCHAR(12) NOT NULL DEFAULT 'Aktif',   -- Aktif / Maintenance
  UNIQUE KEY uq_rak (id_depo, kode_rak),
  FOREIGN KEY (id_depo) REFERENCES depo(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- bagian nadhifatul hilmy ns end

-- bagian lutfi firmansyah start
CREATE TABLE IF NOT EXISTS produk (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_sku VARCHAR(20) NOT NULL UNIQUE,   -- HJ-STR-150
  nama_produk VARCHAR(100) NOT NULL,
  kategori VARCHAR(40) NOT NULL,          -- Jelly Cups / Wafer Roll / Biscuits & Cookies
  isi_per_karton INT NOT NULL DEFAULT 1,
  masa_simpan_bulan INT NOT NULL DEFAULT 12,
  buffer_minimum INT NOT NULL DEFAULT 0,  -- karton, total semua depo
  gambar VARCHAR(150) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;  -- stok TIDAK disimpan di sini, hitung dari batch_stok
-- bagian lutfi firmansyah end

-- bagian nurul khoyimah start
CREATE TABLE IF NOT EXISTS batch_stok (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_produk INT NOT NULL,
  id_depo INT NOT NULL,
  id_rak INT NULL,                        -- NULL = belum masuk rak
  no_batch VARCHAR(30) NOT NULL,
  tgl_masuk DATE NOT NULL,                -- dasar FIFO
  tgl_expired DATE NOT NULL,              -- dasar FEFO
  jumlah INT NOT NULL DEFAULT 0,          -- karton
  UNIQUE KEY uq_batch (no_batch, id_produk, id_depo),
  FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE RESTRICT,
  FOREIGN KEY (id_depo)   REFERENCES depo(id)   ON DELETE RESTRICT,
  FOREIGN KEY (id_rak)    REFERENCES rak(id)    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS mutasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  no_mutasi VARCHAR(20) NOT NULL UNIQUE,  -- MT001
  id_batch INT NOT NULL,                  -- batch asal (produk & depo asal didapat dari JOIN)
  id_depo_tujuan INT NOT NULL,
  jumlah INT NOT NULL,
  tanggal DATE NOT NULL,
  status VARCHAR(12) NOT NULL DEFAULT 'Menunggu',    -- Menunggu / Selesai / Dibatalkan
  id_user INT NOT NULL,
  FOREIGN KEY (id_batch)       REFERENCES batch_stok(id) ON DELETE RESTRICT,
  FOREIGN KEY (id_depo_tujuan) REFERENCES depo(id)       ON DELETE RESTRICT,
  FOREIGN KEY (id_user)        REFERENCES users(id)      ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- bagian nurul khoyimah end

-- ========================================================
-- SEED DATA (MENGGUNAKAN TANGGAL RELATIF CURDATE())
-- ========================================================

-- bagian bersama start
-- 3 Akun Pengguna Uji Sesuai Role
INSERT INTO users (id, username, nama_lengkap, role, password) VALUES
(1, 'admin01', 'Agung Prayitno', 'Kepala Gudang', '$2y$10$P.4D4ouou7nc1JYsFOdRHej/XFfwYM0ntCvrEvr1MLA67TeEksWw6'),
(2, 'staffGD2', 'Budi Santoso', 'Petugas Gudang', '$2y$10$v304OBx1JdOKnnLSy3kqKeYYqSNjYg0xTH5AREwBgsdL5HMkp8F2O'),
(3, 'staffDB3', 'Dewi Lestari', 'Staff Distribusi', '$2y$10$amoLBwmgj8L5I7.CUgHyYOW0f5jQ/X8X5H7umVl3ULgOyrcr2RukS')
ON DUPLICATE KEY UPDATE 
  nama_lengkap = VALUES(nama_lengkap),
  role = VALUES(role),
  password = VALUES(password);
-- bagian bersama end

-- bagian nadhifatul hilmy ns start
-- 4 Depo di Cirebon
INSERT INTO depo (id, kode_depo, nama_depo, jenis_depo, lokasi, status) VALUES
(1, 'GD001', 'Gudang Bahan Baku Utama', 'Bahan Baku', 'Jl. Kalijaga No. 12, Lemahwungkuk, Cirebon', 'Aktif'),
(2, 'GD002', 'Gudang Bahan Kemasan', 'Bahan Kemas', 'Jl. Kalijaga No. 14, Lemahwungkuk, Cirebon', 'Aktif'),
(3, 'GD003', 'Gudang Produk Jadi Cirebon', 'Produk Jadi', 'Kawasan Industri Cirebon Blok B-8', 'Aktif'),
(4, 'GD004', 'Gudang Distribusi Regional', 'Distribusi', 'Jl. Raya Sunan Gunung Jati No. 45, Cirebon', 'Aktif')
ON DUPLICATE KEY UPDATE kode_depo=kode_depo;

-- Rak: GD003 (9 rak), GD004 (3 rak), GD001 (2 rak), GD002 (2 rak)
INSERT INTO rak (id, id_depo, kode_rak, zona, kapasitas, status_rak) VALUES
-- GD003 (Produk Jadi)
(1, 3, 'A-01', 'A', 500, 'Aktif'),
(2, 3, 'A-02', 'A', 500, 'Aktif'),
(3, 3, 'A-03', 'A', 500, 'Aktif'),
(4, 3, 'B-01', 'B', 500, 'Aktif'),
(5, 3, 'B-02', 'B', 500, 'Aktif'),
(6, 3, 'B-03', 'B', 500, 'Aktif'),
(7, 3, 'C-01', 'C', 500, 'Aktif'),
(8, 3, 'C-02', 'C', 500, 'Aktif'),
(9, 3, 'C-03', 'C', 500, 'Maintenance'),
-- GD004 (Distribusi)
(10, 4, 'A-01', 'A', 500, 'Aktif'),
(11, 4, 'B-01', 'B', 500, 'Aktif'),
(12, 4, 'C-01', 'C', 500, 'Aktif'),
-- GD001 (Bahan Baku)
(13, 1, 'A-01', 'A', 400, 'Aktif'),
(14, 1, 'A-02', 'A', 400, 'Aktif'),
-- GD002 (Bahan Kemas)
(15, 2, 'A-01', 'A', 400, 'Aktif'),
(16, 2, 'A-02', 'A', 400, 'Aktif')
ON DUPLICATE KEY UPDATE kode_rak=kode_rak;
-- bagian nadhifatul hilmy ns end

-- bagian lutfi firmansyah start
-- 8 Produk FMCG PT Riyana Cipta Pangan Indonesia
INSERT INTO produk (id, kode_sku, nama_produk, kategori, isi_per_karton, masa_simpan_bulan, buffer_minimum, gambar) VALUES
(1, 'HJ-STR-150', 'Happy Jelly Strawberry 150g', 'Jelly Cups', 24, 12, 500, NULL),
(2, 'HJ-GRP-150', 'Happy Jelly Grape 150g', 'Jelly Cups', 24, 12, 400, NULL),
(3, 'HJ-LYC-150', 'Happy Jelly Lychee 150g', 'Jelly Cups', 24, 12, 400, NULL),
(4, 'HJ-ORG-150', 'Happy Jelly Orange 150g', 'Jelly Cups', 24, 12, 350, NULL),
(5, 'HT-CHC-100', 'Happy Time Chocolate Wafer Roll 100g', 'Wafer Roll', 48, 12, 300, NULL),
(6, 'HT-VAN-100', 'Happy Time Vanilla Roll 100g', 'Wafer Roll', 48, 12, 300, NULL),
(7, 'CT-BC-120', 'Cookies Time Butter Crunch 120g', 'Biscuits & Cookies', 36, 9, 250, NULL),
(8, 'CT-DC-120', 'Cookies Time Double Choco 120g', 'Biscuits & Cookies', 36, 9, 250, NULL)
ON DUPLICATE KEY UPDATE kode_sku=kode_sku;
-- bagian lutfi firmansyah end

-- bagian nurul khoyimah start
-- 12 Batch Stok di GD003 dan GD004 dengan variasi tanggal kedaluwarsa & okupansi
-- Tanggal: 1 lewat (-3 hari), 2 sisa 18 & 25 hari, 2 sisa 36 & 84 hari, sisanya >150 hari
-- Rak: A-01 (475/500 = 95% -> Merah/Penuh), B-01 (200/500 = 40% -> Hijau/Tersedia), A-02 (380/500 = 76% -> Kuning/Optimal)
INSERT INTO batch_stok (id, id_produk, id_depo, id_rak, no_batch, tgl_masuk, tgl_expired, jumlah) VALUES
-- Batch 1: Expired lewat 3 hari (Kritis - Sudah Kedaluwarsa)
(1, 1, 3, 1, 'BATCH-2025-HJ01', DATE_SUB(CURDATE(), INTERVAL 365 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY), 25),

-- Batch 2: Expired 18 hari lagi (Kritis < 30 hari)
(2, 1, 3, 1, 'BATCH-2026-HJ02', DATE_SUB(CURDATE(), INTERVAL 347 DAY), DATE_ADD(CURDATE(), INTERVAL 18 DAY), 450),

-- Batch 3: Expired 25 hari lagi (Kritis < 30 hari)
(3, 2, 3, 2, 'BATCH-2026-HJ03', DATE_SUB(CURDATE(), INTERVAL 340 DAY), DATE_ADD(CURDATE(), INTERVAL 25 DAY), 380),

-- Batch 4: Expired 36 hari lagi (Waspada 31-90 hari)
(4, 5, 3, 4, 'BATCH-2026-HT01', DATE_SUB(CURDATE(), INTERVAL 329 DAY), DATE_ADD(CURDATE(), INTERVAL 36 DAY), 200),

-- Batch 5: Expired 84 hari lagi (Waspada 31-90 hari)
(5, 6, 3, 5, 'BATCH-2026-HT02', DATE_SUB(CURDATE(), INTERVAL 281 DAY), DATE_ADD(CURDATE(), INTERVAL 84 DAY), 180),

-- Batch 6: Expired 180 hari lagi (Aman > 90 hari)
(6, 3, 3, 6, 'BATCH-2026-HJ04', DATE_SUB(CURDATE(), INTERVAL 185 DAY), DATE_ADD(CURDATE(), INTERVAL 180 DAY), 350),

-- Batch 7: Expired 210 hari lagi (Aman > 90 hari)
(7, 4, 3, 7, 'BATCH-2026-HJ05', DATE_SUB(CURDATE(), INTERVAL 155 DAY), DATE_ADD(CURDATE(), INTERVAL 210 DAY), 420),

-- Batch 8: Expired 190 hari lagi (Aman > 90 hari)
(8, 7, 3, 8, 'BATCH-2026-CT01', DATE_SUB(CURDATE(), INTERVAL 80 DAY), DATE_ADD(CURDATE(), INTERVAL 190 DAY), 310),

-- Batch 9: Expired 240 hari lagi (Aman > 90 hari) - di GD004
(9, 1, 4, 10, 'BATCH-2026-HJ06', DATE_SUB(CURDATE(), INTERVAL 125 DAY), DATE_ADD(CURDATE(), INTERVAL 240 DAY), 280),

-- Batch 10: Expired 260 hari lagi (Aman > 90 hari) - di GD004
(10, 8, 4, 11, 'BATCH-2026-CT02', DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 260 DAY), 220),

-- Batch 11: Expired 300 hari lagi (Aman > 90 hari) - belum masuk rak (NULL)
(11, 2, 3, NULL, 'BATCH-2026-HJ07', DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 300 DAY), 60),

-- Batch 12: Expired 320 hari lagi (Aman > 90 hari) - di GD004
(12, 3, 4, 12, 'BATCH-2026-HJ08', DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 320 DAY), 150)
ON DUPLICATE KEY UPDATE no_batch=no_batch;

-- 4 Mutasi Contoh (2 Selesai, 1 Menunggu, 1 Dibatalkan)
INSERT INTO mutasi (id, no_mutasi, id_batch, id_depo_tujuan, jumlah, tanggal, status, id_user) VALUES
(1, 'MT-202605-001', 9, 3, 50, DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'Selesai', 1),
(2, 'MT-202605-002', 8, 4, 40, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'Selesai', 1),
(3, 'MT-202605-003', 2, 4, 100, CURDATE(), 'Menunggu', 1),
(4, 'MT-202605-004', 3, 4, 30, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Dibatalkan', 1)
ON DUPLICATE KEY UPDATE no_mutasi=no_mutasi;
-- bagian nurul khoyimah end

