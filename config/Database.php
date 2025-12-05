<?php
/**
 * Class Database
 * Menangani koneksi ke database MySQL
 */
class Database {
    private $host = "localhost";
    private $db_name = "toko_coklat";
    private $username = "root";
    private $password = "";
    private $conn;
    
    /**
     * Mendapatkan koneksi database
     * @return mysqli
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            if ($this->conn->connect_error) {
                throw new Exception("Koneksi gagal: " . $this->conn->connect_error);
            }
            
            // Set charset ke utf8
            $this->conn->set_charset("utf8");
            
        } catch(Exception $e) {
            echo "Error koneksi: " . $e->getMessage();
        }
        
        return $this->conn;
    }
    
    /**
     * Menutup koneksi database
     */
    public function closeConnection() {
        if($this->conn != null) {
            $this->conn->close();
        }
    }
}
?>