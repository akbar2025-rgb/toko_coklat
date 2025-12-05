<?php
/**
 * Class Transaksi
 * Model untuk mengelola data transaksi penjualan
 */
class Transaksi {
    private $conn;
    private $table_name = "transaksi";
    
    public $id_transaksi;
    public $no_transaksi;
    public $tanggal_transaksi;
    public $id_pelanggan;
    public $id_user;
    public $total_item;
    public $subtotal;
    public $diskon;
    public $total_bayar;
    public $jumlah_bayar;
    public $kembalian;
    public $status;
    
    /**
     * Constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Membaca semua transaksi dengan join
     */
    public function readAll() {
        $query = "SELECT t.*, p.nama_pelanggan, u.nama_lengkap 
                  FROM " . $this->table_name . " t 
                  LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                  LEFT JOIN users u ON t.id_user = u.id_user
                  ORDER BY t.tanggal_transaksi DESC";
        $result = $this->conn->query($query);
        return $result;
    }
    
    /**
     * Membaca transaksi berdasarkan tanggal
     */
    public function readByTanggal($tanggal_mulai, $tanggal_selesai) {
        $query = "SELECT t.*, p.nama_pelanggan, u.nama_lengkap 
                  FROM " . $this->table_name . " t 
                  LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                  LEFT JOIN users u ON t.id_user = u.id_user
                  WHERE DATE(t.tanggal_transaksi) BETWEEN ? AND ?
                  ORDER BY t.tanggal_transaksi DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $tanggal_mulai, $tanggal_selesai);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Membaca satu transaksi
     */
    public function readOne() {
        $query = "SELECT t.*, p.nama_pelanggan, u.nama_lengkap 
                  FROM " . $this->table_name . " t 
                  LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                  LEFT JOIN users u ON t.id_user = u.id_user
                  WHERE t.id_transaksi = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id_transaksi);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if($row) {
            $this->no_transaksi = $row['no_transaksi'];
            $this->tanggal_transaksi = $row['tanggal_transaksi'];
            $this->id_pelanggan = $row['id_pelanggan'];
            $this->id_user = $row['id_user'];
            $this->total_item = $row['total_item'];
            $this->subtotal = $row['subtotal'];
            $this->diskon = $row['diskon'];
            $this->total_bayar = $row['total_bayar'];
            $this->jumlah_bayar = $row['jumlah_bayar'];
            $this->kembalian = $row['kembalian'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }
    
    /**
     * Membaca detail transaksi
     */
    public function readDetail($id_transaksi) {
        $query = "SELECT dt.*, p.nama_produk, p.satuan 
                  FROM detail_transaksi dt
                  LEFT JOIN produk p ON dt.id_produk = p.id_produk
                  WHERE dt.id_transaksi = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id_transaksi);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Menambah transaksi baru
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (no_transaksi, tanggal_transaksi, id_pelanggan, id_user, total_item, subtotal, diskon, total_bayar, jumlah_bayar, kembalian, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("ssiiddddds", 
            $this->no_transaksi,
            $this->tanggal_transaksi,
            $this->id_pelanggan,
            $this->id_user,
            $this->total_item,
            $this->subtotal,
            $this->diskon,
            $this->total_bayar,
            $this->jumlah_bayar,
            $this->kembalian,
            $this->status
        );
        
        if($stmt->execute()) {
            $this->id_transaksi = $this->conn->insert_id;
            return true;
        }
        return false;
    }
    
    /**
     * Menambah detail transaksi
     */
    public function createDetail($id_produk, $harga_jual, $jumlah, $subtotal) {
        $query = "INSERT INTO detail_transaksi (id_transaksi, id_produk, harga_jual, jumlah, subtotal) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iidid", $this->id_transaksi, $id_produk, $harga_jual, $jumlah, $subtotal);
        return $stmt->execute();
    }
    
    /**
     * Update transaksi
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = ?
                  WHERE id_transaksi = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $this->status, $this->id_transaksi);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Hapus transaksi
     */
    public function delete() {
        // Hapus detail dulu
        $query = "DELETE FROM detail_transaksi WHERE id_transaksi = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id_transaksi);
        $stmt->execute();
        
        // Hapus transaksi
        $query = "DELETE FROM " . $this->table_name . " WHERE id_transaksi = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id_transaksi);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Generate nomor transaksi
     */
    public function generateNoTransaksi() {
        $tanggal = date('Ymd');
        $query = "SELECT no_transaksi FROM " . $this->table_name . " 
                  WHERE no_transaksi LIKE 'TRX{$tanggal}%' 
                  ORDER BY id_transaksi DESC LIMIT 1";
        $result = $this->conn->query($query);
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastNo = $row['no_transaksi'];
            $lastNumber = intval(substr($lastNo, -4));
            $newNumber = $lastNumber + 1;
            return "TRX" . $tanggal . str_pad($newNumber, 4, "0", STR_PAD_LEFT);
        } else {
            return "TRX" . $tanggal . "0001";
        }
    }
    
    /**
     * Hitung total transaksi hari ini
     */
    public function countToday() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE DATE(tanggal_transaksi) = CURDATE() AND status = 'selesai'";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Hitung total pendapatan hari ini
     */
    public function sumToday() {
        $query = "SELECT SUM(total_bayar) as total FROM " . $this->table_name . " 
                  WHERE DATE(tanggal_transaksi) = CURDATE() AND status = 'selesai'";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }
}
?>