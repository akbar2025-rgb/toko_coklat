<?php
/**
 * Class Produk
 * Model untuk mengelola data produk coklat
 */
class Produk {
    private $conn;
    private $table_name = "produk";
    
    public $id_produk;
    public $id_kategori;
    public $kode_produk;
    public $nama_produk;
    public $deskripsi;
    public $harga_beli;
    public $harga_jual;
    public $stok;
    public $satuan;
    public $gambar;
    public $status;
    public $created_at;
    public $updated_at;
    
    /**
     * Constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Membaca semua data produk dengan join kategori
     */
    public function readAll() {
        $query = "SELECT p.*, k.nama_kategori 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                  ORDER BY p.nama_produk ASC";
        $result = $this->conn->query($query);
        return $result;
    }
    
    /**
     * Membaca produk berdasarkan kategori
     */
    public function readByKategori($id_kategori) {
        $query = "SELECT p.*, k.nama_kategori 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                  WHERE p.id_kategori = ?
                  ORDER BY p.nama_produk ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id_kategori);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Membaca satu data produk
     */
    public function readOne() {
        $query = "SELECT p.*, k.nama_kategori 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                  WHERE p.id_produk = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id_produk);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if($row) {
            $this->id_kategori = $row['id_kategori'];
            $this->kode_produk = $row['kode_produk'];
            $this->nama_produk = $row['nama_produk'];
            $this->deskripsi = $row['deskripsi'];
            $this->harga_beli = $row['harga_beli'];
            $this->harga_jual = $row['harga_jual'];
            $this->stok = $row['stok'];
            $this->satuan = $row['satuan'];
            $this->gambar = $row['gambar'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }
    
    /**
     * Menambah produk baru
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_kategori, kode_produk, nama_produk, deskripsi, harga_beli, harga_jual, stok, satuan, gambar, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        // Bersihkan data
        $this->nama_produk = htmlspecialchars(strip_tags($this->nama_produk));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        $this->kode_produk = htmlspecialchars(strip_tags($this->kode_produk));
        
        $stmt->bind_param("isssddisss", 
            $this->id_kategori,
            $this->kode_produk,
            $this->nama_produk,
            $this->deskripsi,
            $this->harga_beli,
            $this->harga_jual,
            $this->stok,
            $this->satuan,
            $this->gambar,
            $this->status
        );
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Update data produk
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET id_kategori = ?, kode_produk = ?, nama_produk = ?, deskripsi = ?, 
                      harga_beli = ?, harga_jual = ?, stok = ?, satuan = ?, gambar = ?, status = ?
                  WHERE id_produk = ?";
        $stmt = $this->conn->prepare($query);
        
        // Bersihkan data
        $this->nama_produk = htmlspecialchars(strip_tags($this->nama_produk));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        $this->kode_produk = htmlspecialchars(strip_tags($this->kode_produk));
        
        $stmt->bind_param("isssddisssi", 
            $this->id_kategori,
            $this->kode_produk,
            $this->nama_produk,
            $this->deskripsi,
            $this->harga_beli,
            $this->harga_jual,
            $this->stok,
            $this->satuan,
            $this->gambar,
            $this->status,
            $this->id_produk
        );
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Hapus produk
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_produk = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id_produk);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Generate kode produk otomatis
     */
    public function generateKode() {
        $query = "SELECT kode_produk FROM " . $this->table_name . " ORDER BY id_produk DESC LIMIT 1";
        $result = $this->conn->query($query);
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastKode = $row['kode_produk'];
            $lastNumber = intval(substr($lastKode, 3));
            $newNumber = $lastNumber + 1;
            return "PRD" . str_pad($newNumber, 4, "0", STR_PAD_LEFT);
        } else {
            return "PRD0001";
        }
    }
    
    /**
     * Update stok produk
     */
    public function updateStok($id_produk, $jumlah, $operasi = 'kurang') {
        if($operasi == 'kurang') {
            $query = "UPDATE " . $this->table_name . " SET stok = stok - ? WHERE id_produk = ?";
        } else {
            $query = "UPDATE " . $this->table_name . " SET stok = stok + ? WHERE id_produk = ?";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $jumlah, $id_produk);
        return $stmt->execute();
    }
    
    /**
     * Hitung jumlah produk
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>