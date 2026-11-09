<?php
/**
 * Proses Hapus Pegawai (Delete)
 * 
 * Memproses penghapusan data pegawai.
 * Hanya menerima request POST dari modal konfirmasi.
 */

require_once __DIR__ . '/init.php';
requireLogin();

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pegawai.php');
}

// Validasi CSRF token
if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Token keamanan tidak valid! Silakan coba lagi.');
    redirect('/pegawai.php');
}

// Ambil ID pegawai dari URL
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    setFlash('danger', 'ID pegawai tidak valid!');
    redirect('/pegawai.php');
}

// Inisialisasi model
$pegawaiModel = new Pegawai($db);

// Cek apakah data pegawai ada
$pegawai = $pegawaiModel->getById($id);
if (!$pegawai) {
    setFlash('danger', 'Data pegawai tidak ditemukan!');
    redirect('/pegawai.php');
}

// Proses hapus
try {
    if ($pegawaiModel->delete($id)) {
        setFlash('success', 'Data pegawai "' . $pegawai['nama'] . '" berhasil dihapus!');
    } else {
        setFlash('danger', 'Gagal menghapus data pegawai!');
    }
} catch (Exception $e) {
    setFlash('danger', 'Terjadi kesalahan: ' . $e->getMessage());
}

redirect('/pegawai.php');

