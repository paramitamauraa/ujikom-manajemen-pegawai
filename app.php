<?php
/**
 * Konfigurasi Aplikasi
 * 
 * File ini berisi konstanta dan pengaturan umum aplikasi.
 */

// Nama aplikasi
define('APP_NAME', 'Manajemen Data Pegawai');

// Base URL aplikasi (sesuaikan dengan environment)
define('BASE_URL', '/manajemen-pegawai');

// Jumlah data per halaman (pagination)
define('PER_PAGE', 10);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

