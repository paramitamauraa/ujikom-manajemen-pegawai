<?php
/**
 * Autoloader & Initialization
 * 
 * File ini di-include di setiap halaman utama.
 * Memuat konfigurasi, helper, koneksi database, dan model.
 */

session_start();

// Load konfigurasi
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

// Load helper functions (pendekatan terstruktur)
require_once __DIR__ . '/helpers/functions.php';

// Load models (pendekatan OOP)
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Pegawai.php';

// Inisialisasi koneksi database
$database = new Database();
$db = $database->getConnection();

