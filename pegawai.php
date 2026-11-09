<?php
/**
 * Halaman Daftar Pegawai (Read)
 * 
 * Menampilkan tabel data pegawai dengan fitur:
 * - Pencarian berdasarkan nama/NIP
 * - Pagination
 * - Tombol aksi: Detail, Edit, Hapus
 * - Modal konfirmasi hapus (NFR-01)
 */

require_once __DIR__ . '/init.php';
requireLogin();

$pageTitle = 'Data Pegawai';
$currentPage = 'pegawai';

// Inisialisasi model
$pegawaiModel = new Pegawai($db);

// Ambil parameter dari URL
$page   = max(1, intval($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');

// Ambil data pegawai dengan pagination & search
$result = $pegawaiModel->getAll($page, PER_PAGE, $search);
$pegawaiList = $result['data'];
$totalData   = $result['total'];
$totalPages  = $result['pages'];

// Include header
include __DIR__ . '/views/layouts/header.php';
?>

<!-- Page Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-2">
        <i class="bi bi-person-lines-fill me-2 text-primary"></i>Data Pegawai
    </h4>
    <a href="<?= BASE_URL ?>/pegawai_create.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Tambah Pegawai
    </a>
</div>

<!-- Table Wrapper -->
<div class="table-wrapper">
    <!-- Search Bar & Info -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div class="text-muted mb-2">
            Menampilkan <strong><?= count($pegawaiList) ?></strong> dari <strong><?= $totalData ?></strong> data
        </div>
        <form method="GET" action="" class="search-bar">
            <div class="input-group">
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       value="<?= e($search) ?>" 
                       placeholder="Cari nama atau NIP...">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search"></i>
                </button>
                <?php if (!empty($search)): ?>
                    <a href="<?= BASE_URL ?>/pegawai.php" class="btn btn-outline-secondary" title="Reset">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px">No</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Pendidikan</th>
                    <th>Jabatan</th>
                    <th>Usia</th>
                    <th style="width: 180px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pegawaiList)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <?= !empty($search) ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data pegawai.' ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $no = ($page - 1) * PER_PAGE + 1;
                    foreach ($pegawaiList as $pgw): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><span class="badge bg-secondary"><?= e($pgw['nip']) ?></span></td>
                        <td class="fw-semibold"><?= e($pgw['nama']) ?></td>
                        <td>
                            <?php if ($pgw['jenis_kelamin'] === 'Laki-laki'): ?>
                                <span class="badge bg-info"><i class="bi bi-gender-male me-1"></i>Laki-laki</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="bi bi-gender-female me-1"></i>Perempuan</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($pgw['pendidikan_terakhir']) ?></td>
                        <td><?= e($pgw['jabatan']) ?></td>
                        <td><?= hitungUsia($pgw['tanggal_lahir']) ?> tahun</td>
                        <td class="text-center">
                            <!-- Detail -->
                            <button type="button" 
                                    class="btn btn-sm btn-outline-info btn-action" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailModal<?= $pgw['id'] ?>"
                                    title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </button>
                            <!-- Edit -->
                            <a href="<?= BASE_URL ?>/pegawai_edit.php?id=<?= $pgw['id'] ?>" 
                               class="btn btn-sm btn-outline-warning btn-action" 
                               title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <!-- Hapus -->
                            <button type="button" 
                                    class="btn btn-sm btn-outline-danger btn-action" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal"
                                    onclick="setDeleteData(<?= $pgw['id'] ?>, '<?= e($pgw['nama']) ?>')"
                                    title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Detail Modal -->
                    <div class="modal fade" id="detailModal<?= $pgw['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title">
                                        <i class="bi bi-person-badge me-2"></i>Detail Pegawai
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered detail-table mb-0">
                                        <tr>
                                            <th>NIP</th>
                                            <td><?= e($pgw['nip']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Nama Lengkap</th>
                                            <td><?= e($pgw['nama']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Kelamin</th>
                                            <td><?= e($pgw['jenis_kelamin']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Tempat, Tanggal Lahir</th>
                                            <td><?= e($pgw['tempat_lahir']) ?>, <?= formatTanggal($pgw['tanggal_lahir']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Usia</th>
                                            <td><?= hitungUsia($pgw['tanggal_lahir']) ?> tahun</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td><?= e($pgw['alamat']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>No. Telepon</th>
                                            <td><?= e($pgw['no_telepon']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td><?= e($pgw['email']) ?: '<em class="text-muted">Tidak ada</em>' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Pendidikan Terakhir</th>
                                            <td><?= e($pgw['pendidikan_terakhir']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Jabatan</th>
                                            <td><?= e($pgw['jabatan']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Masuk</th>
                                            <td><?= formatTanggal($pgw['tanggal_masuk']) ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-center mb-0">
            <!-- Previous -->
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- ============================================================ -->
<!-- Modal Konfirmasi Hapus (NFR-01) -->
<!-- ============================================================ -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-confirm">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="warning-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="fw-bold mb-3">Konfirmasi Hapus</h5>
                <p class="text-muted">
                    Apakah Anda yakin ingin menghapus data pegawai 
                    <strong id="deleteNama"></strong>?
                </p>
                <p class="text-muted small">Data yang sudah dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Batal
                </button>
                <form id="deleteForm" method="POST" action="" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-trash me-1"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>

