<?php
/**
 * Konfigurasi Database
 * 
 * File ini berisi class Database untuk mengelola koneksi ke MySQL
 * menggunakan PDO (PHP Data Objects) dengan prepared statements
 * untuk proteksi SQL Injection.
 */

class Database
{
    // Properti koneksi database
    private $host = 'localhost';
    private $db_name = 'manajemen_pegawai';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    /** @var PDO|null Instance koneksi PDO */
    private $conn = null;

    /**
     * Mendapatkan koneksi database
     * 
     * Membuat koneksi PDO ke MySQL dengan konfigurasi yang aman.
     * Menggunakan Singleton pattern sederhana - koneksi hanya dibuat sekali.
     * 
     * @return PDO Instance koneksi PDO
     */
    public function getConnection()
    {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                die("Koneksi database gagal: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
}

