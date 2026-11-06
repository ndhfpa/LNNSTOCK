<?php
// bagian bersama start

class Database {
    private static ?PDO $instance = null;

    /**
     * Pola Singleton Koneksi Database PDO
     * Menggunakan prepared statement native tanpa emulasi untuk keamanan
     */
    public static function connect(): PDO {
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $port = '3306';
            $dbname = 'db_lnnstock';
            $username = 'root';
            $password = '';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                // Catat error teknis ke log server, tampilkan pesan ramah ke pengguna
                error_log("Database Connection Error: " . $e->getMessage());
                die("Maaf, terjadi kendala koneksi ke database LNNSTOCK. Pastikan layanan MySQL/MariaDB pada XAMPP sudah berjalan.");
            }
        }

        return self::$instance;
    }
}

// bagian bersama end

