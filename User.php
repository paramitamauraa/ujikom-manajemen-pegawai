<?php
/**
 * Model User
 * 
 * Class untuk mengelola data user/admin pada tabel `users`.
 * Menggunakan pendekatan OOP (Object-Oriented Programming)
 * dengan PDO prepared statements untuk proteksi SQL Injection.
 */

class User
{
    /** @var PDO Koneksi database */
    private $db;

    /** @var string Nama tabel */
    private $table = 'users';

    // Properti user
    public $id;
    public $username;
    public $password;
    public $nama;

    /**
     * Constructor - menerima koneksi database
     * 
     * @param PDO $db Instance koneksi PDO
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Cari user berdasarkan username
     * 
     * Digunakan saat proses login untuk memverifikasi username.
     * 
     * @param string $username Username yang dicari
     * @return array|false Data user atau false jika tidak ditemukan
     */
    public function findByUsername($username)
    {
        $query = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Verifikasi password
     * 
     * Membandingkan password plain text dengan hash di database
     * menggunakan password_verify() (bcrypt).
     * 
     * @param string $password Password plain text
     * @param string $hashedPassword Password hash dari database
     * @return bool True jika password cocok
     */
    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }

    /**
     * Login user
     * 
     * Memverifikasi username dan password, kemudian membuat session.
     * 
     * @param string $username Username
     * @param string $password Password plain text
     * @return array Hasil login ['success' => bool, 'message' => string]
     */
    public function login($username, $password)
    {
        $user = $this->findByUsername($username);

        if (!$user) {
            return ['success' => false, 'message' => 'Username tidak ditemukan!'];
        }

        if (!$this->verifyPassword($password, $user['password'])) {
            return ['success' => false, 'message' => 'Password salah!'];
        }

        // Set session
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['nama']      = $user['nama'];

        return ['success' => true, 'message' => 'Login berhasil!'];
    }

    /**
     * Logout user
     * 
     * Menghapus session dan menghancurkan session.
     * 
     * @return void
     */
    public function logout()
    {
        session_unset();
        session_destroy();
    }
}

