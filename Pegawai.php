<?php
/**
 * Model Pegawai
 * 
 * Class untuk mengelola data pegawai pada tabel `pegawai`.
 * Menggunakan pendekatan OOP (Object-Oriented Programming)
 * dengan encapsulation dan PDO prepared statements.
 * 
 * Menangani operasi CRUD:
 * - Create  : Tambah data pegawai baru
 * - Read    : Ambil data pegawai (semua, per-ID, pencarian, pagination)
 * - Update  : Ubah data pegawai
 * - Delete  : Hapus data pegawai
 * - Dashboard: Statistik untuk grafik
 */

class Pegawai
{
    /** @var PDO Koneksi database */
    private $db;

    /** @var string Nama tabel */
    private $table = 'pegawai';

    // Properti pegawai (encapsulation)
    private $id;
    private $nip;
    private $nama;
    private $jenis_kelamin;
    private $tempat_lahir;
    private $tanggal_lahir;
    private $alamat;
    private $no_telepon;
    private $email;
    private $pendidikan_terakhir;
    private $jabatan;
    private $tanggal_masuk;

    /**
     * Constructor - menerima koneksi database
     * 
     * @param PDO $db Instance koneksi PDO
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    // =====================================================
    // SETTER METHODS (Encapsulation)
    // =====================================================

    /**
     * Set data pegawai dari array (biasanya dari $_POST)
     * 
     * @param array $data Associative array berisi data pegawai
     * @return void
     */
    public function setData($data)
    {
        $this->nip                  = trim($data['nip'] ?? '');
        $this->nama                 = trim($data['nama'] ?? '');
        $this->jenis_kelamin        = $data['jenis_kelamin'] ?? '';
        $this->tempat_lahir         = trim($data['tempat_lahir'] ?? '');
        $this->tanggal_lahir        = $data['tanggal_lahir'] ?? '';
        $this->alamat               = trim($data['alamat'] ?? '');
        $this->no_telepon           = trim($data['no_telepon'] ?? '');
        $this->email                = trim($data['email'] ?? '') ?: null;
        $this->pendidikan_terakhir  = $data['pendidikan_terakhir'] ?? '';
        $this->jabatan              = trim($data['jabatan'] ?? '');
        $this->tanggal_masuk        = $data['tanggal_masuk'] ?? '';
    }

    // =====================================================
    // VALIDASI
    // =====================================================

    /**
     * Validasi data pegawai
     * 
     * Memeriksa semua field wajib dan format data.
     * 
     * @param int|null $excludeId ID pegawai yang dikecualikan (untuk update)
     * @return array Array berisi pesan error (kosong jika valid)
     */
    public function validate($excludeId = null)
    {
        $errors = [];

        // Validasi NIP - wajib, unik
        if (empty($this->nip)) {
            $errors['nip'] = 'NIP wajib diisi!';
        } else {
            // Cek duplikat NIP
            $query = "SELECT id FROM {$this->table} WHERE nip = :nip";
            if ($excludeId) {
                $query .= " AND id != :id";
            }
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':nip', $this->nip, PDO::PARAM_STR);
            if ($excludeId) {
                $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
            }
            $stmt->execute();
            if ($stmt->fetch()) {
                $errors['nip'] = 'NIP sudah terdaftar!';
            }
        }

        // Validasi Nama - wajib, minimal 3 karakter
        if (empty($this->nama)) {
            $errors['nama'] = 'Nama wajib diisi!';
        } elseif (strlen($this->nama) < 3) {
            $errors['nama'] = 'Nama minimal 3 karakter!';
        }

        // Validasi Jenis Kelamin
        if (!in_array($this->jenis_kelamin, ['Laki-laki', 'Perempuan'])) {
            $errors['jenis_kelamin'] = 'Jenis kelamin tidak valid!';
        }

        // Validasi Tempat Lahir
        if (empty($this->tempat_lahir)) {
            $errors['tempat_lahir'] = 'Tempat lahir wajib diisi!';
        }

        // Validasi Tanggal Lahir - wajib, tidak boleh di masa depan
        if (empty($this->tanggal_lahir)) {
            $errors['tanggal_lahir'] = 'Tanggal lahir wajib diisi!';
        } elseif (strtotime($this->tanggal_lahir) > time()) {
            $errors['tanggal_lahir'] = 'Tanggal lahir tidak boleh di masa depan!';
        }

        // Validasi Alamat
        if (empty($this->alamat)) {
            $errors['alamat'] = 'Alamat wajib diisi!';
        }

        // Validasi No. Telepon - wajib, format numerik, 10-15 digit
        if (empty($this->no_telepon)) {
            $errors['no_telepon'] = 'No. telepon wajib diisi!';
        } elseif (!preg_match('/^[0-9]{10,15}$/', $this->no_telepon)) {
            $errors['no_telepon'] = 'No. telepon harus berupa angka, 10-15 digit!';
        }

        // Validasi Email - opsional, tapi jika diisi harus format valid
        if (!empty($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid!';
        }

        // Validasi Pendidikan Terakhir
        $pendidikanValid = ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'];
        if (!in_array($this->pendidikan_terakhir, $pendidikanValid)) {
            $errors['pendidikan_terakhir'] = 'Pendidikan terakhir tidak valid!';
        }

        // Validasi Jabatan
        if (empty($this->jabatan)) {
            $errors['jabatan'] = 'Jabatan wajib diisi!';
        }

        // Validasi Tanggal Masuk
        if (empty($this->tanggal_masuk)) {
            $errors['tanggal_masuk'] = 'Tanggal masuk wajib diisi!';
        }

        return $errors;
    }

    // =====================================================
    // CRUD OPERATIONS
    // =====================================================

    /**
     * CREATE - Tambah data pegawai baru
     * 
     * @return bool True jika berhasil
     */
    public function create()
    {
        $query = "INSERT INTO {$this->table} 
                  (nip, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, 
                   alamat, no_telepon, email, pendidikan_terakhir, jabatan, tanggal_masuk)
                  VALUES 
                  (:nip, :nama, :jenis_kelamin, :tempat_lahir, :tanggal_lahir, 
                   :alamat, :no_telepon, :email, :pendidikan_terakhir, :jabatan, :tanggal_masuk)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nip', $this->nip);
        $stmt->bindParam(':nama', $this->nama);
        $stmt->bindParam(':jenis_kelamin', $this->jenis_kelamin);
        $stmt->bindParam(':tempat_lahir', $this->tempat_lahir);
        $stmt->bindParam(':tanggal_lahir', $this->tanggal_lahir);
        $stmt->bindParam(':alamat', $this->alamat);
        $stmt->bindParam(':no_telepon', $this->no_telepon);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':pendidikan_terakhir', $this->pendidikan_terakhir);
        $stmt->bindParam(':jabatan', $this->jabatan);
        $stmt->bindParam(':tanggal_masuk', $this->tanggal_masuk);

        return $stmt->execute();
    }

    /**
     * READ - Ambil semua data pegawai dengan pagination dan pencarian
     * 
     * @param int    $page   Halaman saat ini (dimulai dari 1)
     * @param int    $limit  Jumlah data per halaman
     * @param string $search Kata kunci pencarian (nama/NIP)
     * @return array ['data' => array, 'total' => int, 'pages' => int]
     */
    public function getAll($page = 1, $limit = PER_PAGE, $search = '')
    {
        $offset = ($page - 1) * $limit;

        // Query COUNT untuk total data
        $countQuery = "SELECT COUNT(*) as total FROM {$this->table}";
        // Query DATA
        $dataQuery = "SELECT * FROM {$this->table}";

        // Tambahkan kondisi pencarian jika ada
        if (!empty($search)) {
            $searchCondition = " WHERE nama LIKE :search OR nip LIKE :search2";
            $countQuery .= $searchCondition;
            $dataQuery  .= $searchCondition;
        }

        $dataQuery .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

        // Hitung total data
        $countStmt = $this->db->prepare($countQuery);
        if (!empty($search)) {
            $searchParam = "%{$search}%";
            $countStmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
            $countStmt->bindParam(':search2', $searchParam, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];

        // Ambil data
        $dataStmt = $this->db->prepare($dataQuery);
        if (!empty($search)) {
            $searchParam = "%{$search}%";
            $dataStmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
            $dataStmt->bindParam(':search2', $searchParam, PDO::PARAM_STR);
        }
        $dataStmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $dataStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();
        $data = $dataStmt->fetchAll();

        return [
            'data'  => $data,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ];
    }

    /**
     * READ - Ambil data pegawai berdasarkan ID
     * 
     * @param int $id ID pegawai
     * @return array|false Data pegawai atau false jika tidak ditemukan
     */
    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * UPDATE - Ubah data pegawai
     * 
     * @param int $id ID pegawai yang akan diubah
     * @return bool True jika berhasil
     */
    public function update($id)
    {
        $query = "UPDATE {$this->table} SET
                    nip = :nip,
                    nama = :nama,
                    jenis_kelamin = :jenis_kelamin,
                    tempat_lahir = :tempat_lahir,
                    tanggal_lahir = :tanggal_lahir,
                    alamat = :alamat,
                    no_telepon = :no_telepon,
                    email = :email,
                    pendidikan_terakhir = :pendidikan_terakhir,
                    jabatan = :jabatan,
                    tanggal_masuk = :tanggal_masuk
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nip', $this->nip);
        $stmt->bindParam(':nama', $this->nama);
        $stmt->bindParam(':jenis_kelamin', $this->jenis_kelamin);
        $stmt->bindParam(':tempat_lahir', $this->tempat_lahir);
        $stmt->bindParam(':tanggal_lahir', $this->tanggal_lahir);
        $stmt->bindParam(':alamat', $this->alamat);
        $stmt->bindParam(':no_telepon', $this->no_telepon);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':pendidikan_terakhir', $this->pendidikan_terakhir);
        $stmt->bindParam(':jabatan', $this->jabatan);
        $stmt->bindParam(':tanggal_masuk', $this->tanggal_masuk);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * DELETE - Hapus data pegawai
     * 
     * @param int $id ID pegawai yang akan dihapus
     * @return bool True jika berhasil
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // =====================================================
    // DASHBOARD STATISTICS
    // =====================================================

    /**
     * Hitung total pegawai
     * 
     * @return int Total pegawai
     */
    public function getTotalPegawai()
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    /**
     * Statistik pegawai berdasarkan jenis kelamin
     * 
     * @return array Data jumlah pegawai per jenis kelamin
     */
    public function getStatistikJenisKelamin()
    {
        $query = "SELECT jenis_kelamin, COUNT(*) as jumlah 
                  FROM {$this->table} 
                  GROUP BY jenis_kelamin";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Statistik pegawai berdasarkan pendidikan terakhir
     * 
     * @return array Data jumlah pegawai per jenjang pendidikan
     */
    public function getStatistikPendidikan()
    {
        $query = "SELECT pendidikan_terakhir, COUNT(*) as jumlah 
                  FROM {$this->table} 
                  GROUP BY pendidikan_terakhir
                  ORDER BY FIELD(pendidikan_terakhir, 'SD','SMP','SMA/SMK','D3','S1','S2','S3')";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Statistik distribusi usia pegawai
     * 
     * Usia dihitung kalkulatif dari tanggal_lahir.
     * Pengelompokan: < 25, 25-34, 35-44, 45-54, >= 55 tahun
     * 
     * @return array Data jumlah pegawai per kelompok usia
     */
    public function getStatistikUsia()
    {
        $query = "SELECT 
                    CASE 
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 25 THEN '< 25'
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 25 AND 34 THEN '25-34'
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 35 AND 44 THEN '35-44'
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 45 AND 54 THEN '45-54'
                        ELSE '≥ 55'
                    END as kelompok_usia,
                    COUNT(*) as jumlah
                  FROM {$this->table}
                  GROUP BY kelompok_usia
                  ORDER BY FIELD(kelompok_usia, '< 25', '25-34', '35-44', '45-54', '≥ 55')";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

