<?php
/**
 * Helper Functions
 * 
 * Kumpulan fungsi-fungsi utilitas yang digunakan di seluruh aplikasi.
 * Menggunakan pendekatan pemrograman terstruktur (prosedural).
 */

/**
 * Redirect ke URL tertentu
 * 
 * @param string $url URL tujuan redirect
 * @return void
 */
function redirect($url)
{
    header("Location: " . BASE_URL . $url);
    exit();
}

/**
 * Escape output HTML untuk proteksi XSS
 * 
 * @param string|null $string String yang akan di-escape
 * @return string String yang sudah di-escape
 */
function e($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Set flash message ke session
 * 
 * @param string $type Tipe pesan (success, danger, warning, info)
 * @param string $message Isi pesan
 * @return void
 */
function setFlash($type, $message)
{
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Tampilkan flash message (jika ada) dan hapus dari session
 * 
 * @return string HTML notifikasi Bootstrap alert
 */
function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return '<div class="alert alert-' . e($flash['type']) . ' alert-dismissible fade show" role="alert">
                    ' . e($flash['message']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    }
    return '';
}

/**
 * Hitung usia berdasarkan tanggal lahir
 * 
 * Usia dihitung secara kalkulatif dari selisih tanggal lahir
 * dengan tanggal saat ini.
 * 
 * @param string $tanggalLahir Tanggal lahir format Y-m-d
 * @return int Usia dalam tahun
 */
function hitungUsia($tanggalLahir)
{
    $lahir = new DateTime($tanggalLahir);
    $sekarang = new DateTime('today');
    $usia = $lahir->diff($sekarang);
    return $usia->y;
}

/**
 * Format tanggal ke format Indonesia
 * 
 * @param string $date Tanggal format Y-m-d
 * @return string Tanggal format d-m-Y
 */
function formatTanggal($date)
{
    if (empty($date)) return '-';
    return date('d-m-Y', strtotime($date));
}

/**
 * Cek apakah user sudah login
 * 
 * @return bool True jika sudah login
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Proteksi halaman - redirect ke login jika belum login
 * 
 * @return void
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCsrfToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validasi CSRF token
 * 
 * @param string $token Token yang dikirim dari form
 * @return bool True jika valid
 */
function validateCsrfToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

