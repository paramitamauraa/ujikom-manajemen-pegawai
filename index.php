<?php
/**
 * Halaman Dashboard (index.php)
 * 
 * Menampilkan ringkasan statistik data pegawai dengan grafik:
 * 1. Bar Chart - Perbandingan jumlah pegawai berdasarkan jenis kelamin
 * 2. Pie Chart - Distribusi pendidikan terakhir
 * 3. Bar Chart - Distribusi usia pegawai
 */

require_once __DIR__ . '/init.php';
requireLogin();

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

// Inisialisasi model Pegawai
$pegawaiModel = new Pegawai($db);

// Ambil data statistik
$totalPegawai       = $pegawaiModel->getTotalPegawai();
$statJenisKelamin   = $pegawaiModel->getStatistikJenisKelamin();
$statPendidikan     = $pegawaiModel->getStatistikPendidikan();
$statUsia           = $pegawaiModel->getStatistikUsia();

// Hitung jumlah laki-laki dan perempuan
$jumlahLakiLaki  = 0;
$jumlahPerempuan = 0;
foreach ($statJenisKelamin as $item) {
    if ($item['jenis_kelamin'] === 'Laki-laki') {
        $jumlahLakiLaki = $item['jumlah'];
    } else {
        $jumlahPerempuan = $item['jumlah'];
    }
}

// Siapkan data untuk Chart.js (format JSON)
$jkLabels = array_column($statJenisKelamin, 'jenis_kelamin');
$jkData   = array_column($statJenisKelamin, 'jumlah');

$pendLabels = array_column($statPendidikan, 'pendidikan_terakhir');
$pendData   = array_column($statPendidikan, 'jumlah');

$usiaLabels = array_column($statUsia, 'kelompok_usia');
$usiaData   = array_column($statUsia, 'jumlah');

// Include header
include __DIR__ . '/views/layouts/header.php';
?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Total Pegawai -->
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-number text-primary"><?= $totalPegawai ?></div>
                    <div class="stat-label">Total Pegawai</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Laki-laki -->
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="bi bi-gender-male"></i>
                </div>
                <div>
                    <div class="stat-number text-info"><?= $jumlahLakiLaki ?></div>
                    <div class="stat-label">Laki-laki</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Perempuan -->
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="bi bi-gender-female"></i>
                </div>
                <div>
                    <div class="stat-number text-danger"><?= $jumlahPerempuan ?></div>
                    <div class="stat-label">Perempuan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <!-- Bar Chart: Jenis Kelamin -->
    <div class="col-lg-6">
        <div class="card chart-card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart-fill me-2 text-primary"></i>
                Perbandingan Jenis Kelamin
            </div>
            <div class="card-body">
                <canvas id="chartJenisKelamin"></canvas>
            </div>
        </div>
    </div>

    <!-- Pie Chart: Pendidikan Terakhir -->
    <div class="col-lg-6">
        <div class="card chart-card h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill me-2 text-success"></i>
                Distribusi Pendidikan Terakhir
            </div>
            <div class="card-body">
                <canvas id="chartPendidikan"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Bar Chart: Distribusi Usia -->
    <div class="col-lg-8 mx-auto">
        <div class="card chart-card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart-line-fill me-2 text-warning"></i>
                Distribusi Usia Pegawai
            </div>
            <div class="card-body">
                <canvas id="chartUsia"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
// Script Chart.js - ditaruh sebagai extra scripts
$extraScripts = '
<script>
// ============================================================
// Warna-warna untuk grafik
// ============================================================
const colors = {
    primary: "rgba(13, 110, 253, 0.8)",
    info:    "rgba(13, 202, 240, 0.8)",
    danger:  "rgba(220, 53, 69, 0.8)",
    success: "rgba(25, 135, 84, 0.8)",
    warning: "rgba(255, 193, 7, 0.8)",
    purple:  "rgba(111, 66, 193, 0.8)",
    teal:    "rgba(32, 201, 151, 0.8)",
    orange:  "rgba(253, 126, 20, 0.8)",
};

const colorArray = [
    colors.primary, colors.info, colors.danger, colors.success, 
    colors.warning, colors.purple, colors.teal, colors.orange
];

// ============================================================
// 1. Bar Chart - Jenis Kelamin
// ============================================================
new Chart(document.getElementById("chartJenisKelamin"), {
    type: "bar",
    data: {
        labels: ' . json_encode($jkLabels) . ',
        datasets: [{
            label: "Jumlah Pegawai",
            data: ' . json_encode($jkData) . ',
            backgroundColor: [colors.info, colors.danger],
            borderColor: [colors.info, colors.danger],
            borderWidth: 1,
            borderRadius: 8,
            barThickness: 60,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// ============================================================
// 2. Pie Chart - Pendidikan Terakhir
// ============================================================
new Chart(document.getElementById("chartPendidikan"), {
    type: "pie",
    data: {
        labels: ' . json_encode($pendLabels) . ',
        datasets: [{
            data: ' . json_encode($pendData) . ',
            backgroundColor: colorArray.slice(0, ' . count($pendLabels) . '),
            borderWidth: 2,
            borderColor: "#fff"
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: "bottom",
                labels: { padding: 15, usePointStyle: true }
            }
        }
    }
});

// ============================================================
// 3. Bar Chart - Distribusi Usia
// ============================================================
new Chart(document.getElementById("chartUsia"), {
    type: "bar",
    data: {
        labels: ' . json_encode($usiaLabels) . ',
        datasets: [{
            label: "Jumlah Pegawai",
            data: ' . json_encode($usiaData) . ',
            backgroundColor: colors.warning,
            borderColor: colors.orange,
            borderWidth: 1,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            },
            x: {
                title: {
                    display: true,
                    text: "Kelompok Usia (tahun)"
                }
            }
        }
    }
});
</script>
';

include __DIR__ . '/views/layouts/footer.php';
?>

