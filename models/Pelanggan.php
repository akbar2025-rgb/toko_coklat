<?php
/**
 * Class Pelanggan
 * Model untuk mengelola data pelanggan
 */
class Pelanggan {
    private $conn;
    private $table_name = "pelanggan";
    
    public $id_pelanggan;
    public $kode_pelanggan;
    public $nama_pelanggan;
    public $alamat;
    public $telepon;
    public $email;
    public $created_at;
    public $updated_at;
    
    /**
     * Constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Membaca semua data pelanggan
     */
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nama_pelanggan ASC";
        $result = $this->conn->query($query);
        return $result;
    }
    
    /**
     * Membaca satu data pelanggan
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_pelanggan = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id_pelanggan);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if($row) {
            $this->kode_pelanggan = $row['kode_pelanggan'];
            $this->nama_pelanggan = $row['nama_pelanggan'];
            $this->alamat = $row['alamat'];
            $this->telepon = $row['telepon'];
            $this->email = $row['email'];
            return true;
        }
        return false;
    }
    
    /**
     * Menambah pelanggan baru
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (kode_pelanggan, nama_pelanggan, alamat, telepon, email) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        // Bersihkan data
        $this->nama_pelanggan = htmlspecialchars(strip_tags($this->nama_pelanggan));
        $this->alamat = htmlspecialchars(strip_tags($this->alamat));
        
        $stmt->bind_param("sssss", 
            $this->kode_pelanggan,
            $this->nama_pelanggan,
            $this->alamat,
            $this->telepon,
            $this->email
        );
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Update data pelanggan
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET kode_pelanggan = ?, nama_pelanggan = ?, alamat = ?, telepon = ?, email = ?
                  WHERE id_pelanggan = ?";
        $stmt = $this->conn->prepare($query);
        
        // Bersihkan data
        $this->nama_pelanggan = htmlspecialchars(strip_tags($this->nama_pelanggan));
        $this->alamat = htmlspecialchars(strip_tags($this->alamat));
        
        $stmt->bind_param("sssssi", 
            $this->kode_pelanggan,
            $this->nama_pelanggan,
            $this->alamat,
            $this->telepon,
            $this->email,
            $this->id_pelanggan
        );
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Hapus pelanggan
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_pelanggan = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id_pelanggan);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Generate kode pelanggan otomatis
     */
    public function generateKode() {
        $query = "SELECT kode_pelanggan FROM " . $this->table_name . " ORDER BY id_pelanggan DESC LIMIT 1";
        $result = $this->conn->query($query);
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastKode = $row['kode_pelanggan'];
            $lastNumber = intval(substr($lastKode, 3));
            $newNumber = $lastNumber + 1;
            return "PLG" . str_pad($newNumber, 3, "0", STR_PAD_LEFT);
        } else {
            return "PLG001";
        }
    }
    
    /**
     * Hitung jumlah pelanggan
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>