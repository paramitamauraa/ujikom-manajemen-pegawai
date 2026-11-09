<?php
/**
 * Halaman Tambah Pegawai (Create)
 * 
 * Form untuk menambahkan data pegawai baru ke database.
 * Validasi dilakukan di sisi server (Model) dan client (HTML5).
 */

require_once __DIR__ . '/init.php';
requireLogin();

$pageTitle = 'Tambah Pegawai';
$currentPage = 'pegawai';

// Inisialisasi model
$pegawaiModel = new Pegawai($db);

$errors = [];
$old = []; // Data lama untuk re-populate form jika error

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simpan data lama
    $old = $_POST;

    // Set data ke model
    $pegawaiModel->setData($_POST);

    // Validasi
    $errors = $pegawaiModel->validate();

    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        try {
            if ($pegawaiModel->create()) {
                setFlash('success', 'Data pegawai berhasil ditambahkan!');
                redirect('/pegawai.php');
            } else {
                setFlash('danger', 'Gagal menambahkan data pegawai!');
            }
        } catch (Exception $e) {
            setFlash('danger', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

// Include header
include __DIR__ . '/views/layouts/header.php';
?>

<!-- Page Header -->
<div class="mb-4">
    <a href="<?= BASE_URL ?>/pegawai.php" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Pegawai
    </a>
    <h4 class="fw-bold">
        <i class="bi bi-person-plus-fill me-2 text-primary"></i>Tambah Pegawai Baru
    </h4>
</div>

<!-- Form Card -->
<div class="card form-card">
    <div class="card-header">
        <i class="bi bi-clipboard-plus me-2"></i>Form Data Pegawai
    </div>
    <div class="card-body p-4">
        <form method="POST" action="" data-loading="true">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

            <div class="row g-3">
                <!-- NIP -->
                <div class="col-md-6">
                    <label for="nip" class="form-label fw-semibold">
                        NIP / ID Pegawai <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control <?= isset($errors['nip']) ? 'is-invalid' : '' ?>" 
                           id="nip" name="nip" 
                           value="<?= e($old['nip'] ?? '') ?>"
                           maxlength="20" required>
                    <?php if (isset($errors['nip'])): ?>
                        <div class="invalid-feedback"><?= e($errors['nip']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Nama Lengkap -->
                <div class="col-md-6">
                    <label for="nama" class="form-label fw-semibold">
                        Nama Lengkap <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>" 
                           id="nama" name="nama" 
                           value="<?= e($old['nama'] ?? '') ?>"
                           minlength="3" maxlength="100" required>
                    <?php if (isset($errors['nama'])): ?>
                        <div class="invalid-feedback"><?= e($errors['nama']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Jenis Kelamin -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Jenis Kelamin <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex gap-4 mt-1">
                        <div class="form-check">
                            <input class="form-check-input <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>" 
                                   type="radio" name="jenis_kelamin" id="jkLaki" value="Laki-laki"
                                   <?= ($old['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="jkLaki">Laki-laki</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="radio" name="jenis_kelamin" id="jkPerempuan" value="Perempuan"
                                   <?= ($old['jenis_kelamin'] ?? '') === 'Perempuan' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="jkPerempuan">Perempuan</label>
                        </div>
                    </div>
                    <?php if (isset($errors['jenis_kelamin'])): ?>
                        <div class="text-danger small mt-1"><?= e($errors['jenis_kelamin']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Pendidikan Terakhir -->
                <div class="col-md-6">
                    <label for="pendidikan_terakhir" class="form-label fw-semibold">
                        Pendidikan Terakhir <span class="text-danger">*</span>
                    </label>
                    <select class="form-select <?= isset($errors['pendidikan_terakhir']) ? 'is-invalid' : '' ?>" 
                            id="pendidikan_terakhir" name="pendidikan_terakhir" required>
                        <option value="">-- Pilih Pendidikan --</option>
                        <?php
                        $pendidikanOptions = ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'];
                        foreach ($pendidikanOptions as $opt):
                        ?>
                            <option value="<?= $opt ?>" <?= ($old['pendidikan_terakhir'] ?? '') === $opt ? 'selected' : '' ?>>
                                <?= $opt ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['pendidikan_terakhir'])): ?>
                        <div class="invalid-feedback"><?= e($errors['pendidikan_terakhir']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Tempat Lahir -->
                <div class="col-md-6">
                    <label for="tempat_lahir" class="form-label fw-semibold">
                        Tempat Lahir <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control <?= isset($errors['tempat_lahir']) ? 'is-invalid' : '' ?>" 
                           id="tempat_lahir" name="tempat_lahir" 
                           value="<?= e($old['tempat_lahir'] ?? '') ?>"
                           maxlength="50" required>
                    <?php if (isset($errors['tempat_lahir'])): ?>
                        <div class="invalid-feedback"><?= e($errors['tempat_lahir']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Tanggal Lahir -->
                <div class="col-md-6">
                    <label for="tanggal_lahir" class="form-label fw-semibold">
                        Tanggal Lahir <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control <?= isset($errors['tanggal_lahir']) ? 'is-invalid' : '' ?>" 
                           id="tanggal_lahir" name="tanggal_lahir" 
                           value="<?= e($old['tanggal_lahir'] ?? '') ?>"
                           max="<?= date('Y-m-d') ?>" required>
                    <?php if (isset($errors['tanggal_lahir'])): ?>
                        <div class="invalid-feedback"><?= e($errors['tanggal_lahir']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Alamat -->
                <div class="col-12">
                    <label for="alamat" class="form-label fw-semibold">
                        Alamat <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control <?= isset($errors['alamat']) ? 'is-invalid' : '' ?>" 
                              id="alamat" name="alamat" rows="3" required><?= e($old['alamat'] ?? '') ?></textarea>
                    <?php if (isset($errors['alamat'])): ?>
                        <div class="invalid-feedback"><?= e($errors['alamat']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- No. Telepon -->
                <div class="col-md-6">
                    <label for="no_telepon" class="form-label fw-semibold">
                        No. Telepon <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control <?= isset($errors['no_telepon']) ? 'is-invalid' : '' ?>" 
                           id="no_telepon" name="no_telepon" 
                           value="<?= e($old['no_telepon'] ?? '') ?>"
                           pattern="[0-9]{10,15}" maxlength="15"
                           placeholder="Contoh: 081234567890" required>
                    <?php if (isset($errors['no_telepon'])): ?>
                        <div class="invalid-feedback"><?= e($errors['no_telepon']) ?></div>
                    <?php endif; ?>
                    <div class="form-text">Format: 10-15 digit angka</div>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">
                        Email <span class="text-muted fw-normal">(opsional)</span>
                    </label>
                    <input type="email" 
                           class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                           id="email" name="email" 
                           value="<?= e($old['email'] ?? '') ?>"
                           maxlength="100"
                           placeholder="contoh@email.com">
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= e($errors['email']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Jabatan -->
                <div class="col-md-6">
                    <label for="jabatan" class="form-label fw-semibold">
                        Jabatan <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control <?= isset($errors['jabatan']) ? 'is-invalid' : '' ?>" 
                           id="jabatan" name="jabatan" 
                           value="<?= e($old['jabatan'] ?? '') ?>"
                           maxlength="50" required>
                    <?php if (isset($errors['jabatan'])): ?>
                        <div class="invalid-feedback"><?= e($errors['jabatan']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Tanggal Masuk -->
                <div class="col-md-6">
                    <label for="tanggal_masuk" class="form-label fw-semibold">
                        Tanggal Masuk <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control <?= isset($errors['tanggal_masuk']) ? 'is-invalid' : '' ?>" 
                           id="tanggal_masuk" name="tanggal_masuk" 
                           value="<?= e($old['tanggal_masuk'] ?? '') ?>" required>
                    <?php if (isset($errors['tanggal_masuk'])): ?>
                        <div class="invalid-feedback"><?= e($errors['tanggal_masuk']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Buttons -->
            <hr class="my-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>/pegawai.php" class="btn btn-secondary px-4">
                    <i class="bi bi-x-lg me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>

