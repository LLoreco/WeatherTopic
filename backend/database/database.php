<?php
require_once __DIR__ . '/../../logs/logger.php';

class Database{
    private $host = 'localhost';
    private $db_name = 'WeatherPHP';
    private $user = 'postgres';
    private $password = 'Abcdefg111';
    private $connection;
    private $logger;
    public function __construct() {
        $this->logger = new Logger();
    }

    public function connectDB(){
        $this->connection = null;
        try{
            $this->connection = new PDO(
                "pgsql: host=" . $this->host . "; dbname=" . $this->db_name,
                $this->user,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->connection;
        } catch (PDOException $e){
            $this->logger->logErrorUser("Failed connection to database");
            echo "Database connection error: " . $e->getMessage();
            return null;
        }
    }
}
?>