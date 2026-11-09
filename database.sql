-- ============================================================
-- Database: manajemen_pegawai
-- Deskripsi: Script SQL untuk membuat database dan tabel
--            sistem manajemen data pegawai (Ujikom JWP)
-- ============================================================

CREATE DATABASE IF NOT EXISTS manajemen_pegawai
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE manajemen_pegawai;

-- ------------------------------------------------------------
-- Tabel users: autentikasi admin
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hashed dengan password_hash()',
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabel pegawai: data pegawai
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pegawai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(20) NOT NULL UNIQUE COMMENT 'Nomor Induk Pegawai',
    nama VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    tempat_lahir VARCHAR(50) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    alamat TEXT NOT NULL,
    no_telepon VARCHAR(15) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    pendidikan_terakhir ENUM('SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3') NOT NULL,
    jabatan VARCHAR(50) NOT NULL,
    tanggal_masuk DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Data awal: Admin default
-- Password: admin123 (hashed dengan password_hash)
-- ------------------------------------------------------------
INSERT INTO users (username, password, nama) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- ------------------------------------------------------------
-- Data contoh: Pegawai (opsional, untuk testing)
-- ------------------------------------------------------------
INSERT INTO pegawai (nip, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, no_telepon, email, pendidikan_terakhir, jabatan, tanggal_masuk) VALUES
('NIP001', 'Budi Santoso', 'Laki-laki', 'Jakarta', '1990-05-15', 'Jl. Merdeka No. 10, Jakarta Pusat', '081234567890', 'budi@email.com', 'S1', 'Manager IT', '2015-01-10'),
('NIP002', 'Siti Nurhaliza', 'Perempuan', 'Bandung', '1995-08-22', 'Jl. Asia Afrika No. 5, Bandung', '082345678901', 'siti@email.com', 'S1', 'Staff Keuangan', '2018-03-01'),
('NIP003', 'Ahmad Fauzi', 'Laki-laki', 'Surabaya', '1988-12-03', 'Jl. Pahlawan No. 20, Surabaya', '083456789012', NULL, 'S2', 'Kepala Divisi', '2012-07-15'),
('NIP004', 'Dewi Lestari', 'Perempuan', 'Yogyakarta', '1992-03-18', 'Jl. Malioboro No. 8, Yogyakarta', '084567890123', 'dewi@email.com', 'D3', 'Staff Admin', '2019-06-20'),
('NIP005', 'Rizky Pratama', 'Laki-laki', 'Semarang', '2000-11-25', 'Jl. Pandanaran No. 15, Semarang', '085678901234', NULL, 'SMA/SMK', 'Office Boy', '2022-01-05'),
('NIP006', 'Nadia Putri', 'Perempuan', 'Medan', '1985-07-10', 'Jl. Imam Bonjol No. 3, Medan', '086789012345', 'nadia@email.com', 'S2', 'HRD Manager', '2010-09-12'),
('NIP007', 'Fajar Hidayat', 'Laki-laki', 'Makassar', '1998-01-30', 'Jl. Sultan Alauddin No. 7, Makassar', '087890123456', NULL, 'S1', 'Staff Marketing', '2021-04-18'),
('NIP008', 'Rina Wati', 'Perempuan', '   Palembang', '1993-09-05', 'Jl. Jenderal Sudirman No. 12, Palembang', '088901234567', 'rina@email.com', 'D3', 'Sekretaris', '2017-11-01'),
('NIP009', 'Doni Saputra', 'Laki-laki', 'Denpasar', '1975-04-20', 'Jl. Sunset Road No. 9, Denpasar', '089012345678', NULL, 'S1', 'Direktur Operasional', '2005-02-28'),
('NIP010', 'Maya Sari', 'Perempuan', 'Manado', '2001-06-14', 'Jl. Sam Ratulangi No. 6, Manado', '081123456789', 'maya@email.com', 'SMA/SMK', 'Resepsionis', '2023-08-10');

