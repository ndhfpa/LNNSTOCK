<?php
// bagian bersama start

/**
 * Escape string untuk mencegah serangan XSS (Cross-Site Scripting)
 */
function e(?string $value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Menghasilkan URL dasar dinamis yang kompatibel dengan XAMPP maupun PHP built-in server
 */
function base_url(string $path = ''): string {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $scriptDir = str_replace('\\', '/', $scriptDir);
    $base = rtrim($scriptDir, '/') . '/';
    
    if ($base === '//' || $base === '.' || $base === './' || $base === '') {
        $base = '/';
    }

    if ($path === '') {
        return $base;
    }

    return $base . ltrim($path, '/');
}

/**
 * Format tanggal ke Bahasa Indonesia (contoh: 24 Mei 2026 atau Kamis, 24 Mei 2026)
 */
function format_date_id(?string $dateStr, bool $withDay = false): string {
    if (!$dateStr || $dateStr === '0000-00-00') {
        return '-';
    }

    $timestamp = strtotime($dateStr);
    if (!$timestamp) {
        return $dateStr;
    }

    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $hari = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    ];

    $d = date('j', $timestamp);
    $m = (int)date('n', $timestamp);
    $y = date('Y', $timestamp);
    $namaBulan = $bulan[$m] ?? date('M', $timestamp);

    if ($withDay) {
        $dayName = $hari[date('l', $timestamp)] ?? date('l', $timestamp);
        return "{$dayName}, {$d} {$namaBulan} {$y}";
    }

    return "{$d} {$namaBulan} {$y}";
}

/**
 * Format angka ribuan dengan pemisah titik
 */
function format_number(float|int|string|null $number): string {
    return number_format((float)($number ?? 0), 0, ',', '.');
}

/**
 * Mengambil token CSRF saat ini
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Menghasilkan input hidden CSRF Token
 */
function csrf_field(): string {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Menyimpan pesan flash ke sesi
 */
function flash(string $key, string $message): void {
    $_SESSION['flash'][$key] = $message;
}

/**
 * Memeriksa apakah ada pesan flash tertentu
 */
function flash_has(string $key): bool {
    return isset($_SESSION['flash'][$key]);
}

/**
 * Mengambil pesan flash dan langsung menghapusnya dari sesi
 */
function flash_get(string $key): ?string {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

// bagian bersama end

