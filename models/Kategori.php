<?php
/**
 * Class Kategori
 * Model untuk mengelola data kategori produk
 */
class Kategori {
    private $conn;
    private $table_name = "kategori";
    
    public $id_kategori;
    public $nama_kategori;
    public $deskripsi;
    public $created_at;
    public $updated_at;
    
    /**
     * Constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Membaca semua data kategori
     */
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nama_kategori ASC";
        $result = $this->conn->query($query);
        return $result;
    }
    
    /**
     * Membaca satu data kategori berdasarkan ID
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_kategori = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id_kategori);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if($row) {
            $this->nama_kategori = $row['nama_kategori'];
            $this->deskripsi = $row['deskripsi'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }
    
    /**
     * Menambah data kategori baru
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nama_kategori, deskripsi) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        
        // Bersihkan data
        $this->nama_kategori = htmlspecialchars(strip_tags($this->nama_kategori));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        
        $stmt->bind_param("ss", $this->nama_kategori, $this->deskripsi);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Update data kategori
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nama_kategori = ?, deskripsi = ? WHERE id_kategori = ?";
        $stmt = $this->conn->prepare($query);
        
        // Bersihkan data
        $this->nama_kategori = htmlspecialchars(strip_tags($this->nama_kategori));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        
        $stmt->bind_param("ssi", $this->nama_kategori, $this->deskripsi, $this->id_kategori);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Hapus data kategori
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_kategori = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id_kategori);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Hitung jumlah kategori
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>