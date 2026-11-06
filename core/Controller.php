<?php
// bagian bersama start

require_once __DIR__ . '/Permission.php';

abstract class Controller {
    /**
     * Render view dengan atau tanpa layout utama
     */
    protected function view(string $viewPath, array $data = [], bool $withLayout = true): void {
        // Ekstrak data agar menjadi variabel di view
        extract($data);

        if ($withLayout) {
            // Ambil data global untuk badge topbar dan sidebar
            $db = Database::connect();
            
            // Hitung mutasi berstatus Menunggu untuk badge sidebar
            $stmtPending = $db->query("SELECT COUNT(*) AS total FROM mutasi WHERE status = 'Menunggu'");
            $pendingMutasiCount = (int)($stmtPending->fetch()['total'] ?? 0);

            // Hitung batch kritis (expired <= 30 hari atau sudah lewat) untuk lonceng topbar
            $stmtKritis = $db->query("SELECT COUNT(*) AS total FROM batch_stok WHERE jumlah > 0 AND DATEDIFF(tgl_expired, CURDATE()) <= 30");
            $kritisBatchCount = (int)($stmtKritis->fetch()['total'] ?? 0);

            $currentUser = AuthGuard::user();
            $currentPage = $_GET['page'] ?? 'dashboard';

            require_once __DIR__ . '/../views/layouts/header.php';
            require_once __DIR__ . '/../views/layouts/sidebar.php';
            require_once __DIR__ . '/../views/layouts/topbar.php';
            require_once __DIR__ . '/../views/' . $viewPath . '.php';
            require_once __DIR__ . '/../views/layouts/footer.php';
        } else {
            require_once __DIR__ . '/../views/' . $viewPath . '.php';
        }
    }

    /**
     * Alihkan halaman ke URL tertentu
     */
    protected function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Menyimpan pesan flash ke sesi
     */
    protected function flash(string $key, string $message): void {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Verifikasi token CSRF dari request POST
     */
    protected function verifyCsrf(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die("Metode request tidak diizinkan.");
        }

        $sessionToken = $_SESSION['csrf_token'] ?? '';
        $postToken = $_POST['csrf_token'] ?? '';

        if (empty($sessionToken) || empty($postToken) || !hash_equals($sessionToken, $postToken)) {
            http_response_code(403);
            die("Akses ditolak: Token CSRF tidak valid atau telah kedaluwarsa. Silakan refresh halaman dan coba lagi.");
        }
    }
}

// bagian bersama end

