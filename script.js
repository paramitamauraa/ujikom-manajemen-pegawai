/**
 * Custom JavaScript - Manajemen Data Pegawai
 * 
 * File ini berisi fungsi-fungsi JavaScript untuk:
 * - Konfirmasi hapus (modal)
 * - Loading indicator
 * - Validasi form client-side
 */

// ============================================================
// Loading Overlay
// ============================================================

/**
 * Tampilkan loading overlay
 */
function showLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.add('active');
    }
}

/**
 * Sembunyikan loading overlay
 */
function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.remove('active');
    }
}

// ============================================================
// Konfirmasi Hapus (Modal) - NFR-01
// ============================================================

/**
 * Set data untuk modal konfirmasi hapus
 * Dipanggil saat tombol "Hapus" diklik
 * 
 * @param {number} id    - ID pegawai
 * @param {string} nama  - Nama pegawai (untuk pesan konfirmasi)
 */
function setDeleteData(id, nama) {
    const deleteForm = document.getElementById('deleteForm');
    const deleteNama = document.getElementById('deleteNama');

    if (deleteForm && deleteNama) {
        deleteForm.action = 'pegawai_delete.php?id=' + id;
        deleteNama.textContent = nama;
    }
}

// ============================================================
// Auto-dismiss Alert
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    // Auto-dismiss alert setelah 5 detik
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });

    // Loading indicator saat form submit
    const forms = document.querySelectorAll('form[data-loading="true"]');
    forms.forEach(function (form) {
        form.addEventListener('submit', function () {
            showLoading();
        });
    });
});

// ============================================================
// Client-side Validation Helper
// ============================================================

/**
 * Validasi format nomor telepon
 * 
 * @param {string} phone - Nomor telepon
 * @returns {boolean} True jika valid
 */
function validatePhone(phone) {
    const regex = /^[0-9]{10,15}$/;
    return regex.test(phone);
}

/**
 * Validasi format email
 * 
 * @param {string} email - Alamat email
 * @returns {boolean} True jika valid
 */
function validateEmail(email) {
    if (email === '') return true; // Email opsional
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Validasi tanggal tidak di masa depan
 * 
 * @param {string} dateStr - Tanggal format YYYY-MM-DD
 * @returns {boolean} True jika tanggal tidak di masa depan
 */
function validatePastDate(dateStr) {
    const inputDate = new Date(dateStr);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return inputDate <= today;
}

