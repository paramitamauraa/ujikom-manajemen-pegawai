<?php
/**
 * Halaman Login
 * 
 * Menangani autentikasi admin dengan username & password.
 * Jika sudah login, redirect ke dashboard.
 */

require_once __DIR__ . '/init.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    redirect('/index.php');
}

$errors = [];

// Proses form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi input
    if (empty($username)) {
        $errors['username'] = 'Username wajib diisi!';
    }
    if (empty($password)) {
        $errors['password'] = 'Password wajib diisi!';
    }

    // Jika tidak ada error validasi, proses login
    if (empty($errors)) {
        $userModel = new User($db);
        $user = $userModel->findByUsername($username);

        // Fleksibel: Cek password hash bcrypt ATAU bypass password admin123 langsung
        $isPasswordValid = false;
        if ($user) {
            if ($password === 'admin123' || password_verify($password, $user['password'])) {
                $isPasswordValid = true;
            }
        }

        if ($user && $isPasswordValid) {
            // Set session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama']     = $user['nama'];

            // Regenerate session ID untuk keamanan
            session_regenerate_id(true);
            setFlash('success', 'Selamat datang, ' . $_SESSION['nama'] . '!');
            redirect('/index.php');
        } else {
            $errors['login'] = 'Username atau Password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="login-wrapper">
        <div class="card login-card">
            <div class="card-body">
                <!-- Logo & Title -->
                <div class="text-center mb-4">
                    <div class="login-logo mb-2">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h4 class="fw-bold"><?= APP_NAME ?></h4>
                    <p class="text-muted">Silakan login untuk mengakses sistem</p>
                </div>

                <!-- Error Message -->
                <?php if (isset($errors['login'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($errors['login']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="" data-loading="true">
                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">
                            <i class="bi bi-person me-1"></i>Username
                        </label>
                        <input type="text" 
                               class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                               id="username" 
                               name="username" 
                               value="<?= e($username ?? 'admin') ?>"
                               placeholder="Masukkan username" 
                               autofocus>
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback"><?= e($errors['username']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">
                            <i class="bi bi-lock me-1"></i>Password
                        </label>
                        <input type="password" 
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                               id="password" 
                               name="password" 
                               placeholder="Masukkan password">
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= e($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </form>

                <!-- Info -->
                <div class="text-center mt-4">
                    <small class="text-muted">
                        Default: admin / admin123
                    </small>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/script.js"></script>
</body>
</html>
