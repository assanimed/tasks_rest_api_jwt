<?php
    namespace App\DataBase;

    

    class Database {
        private ?\PDO $conn = null;

        public function __construct(
            private string $host,
            private string $db,
            private string $port,
            private string $user,
            private string $pwd
        ){}
        
        public function getConnection(): \PDO{

            if($this->conn === null)
            {
                $dsn = "mysql:host={$this->host};dbname={$this->db};port={$this->port};charset=utf8mb4";
            
                $this -> conn = new \PDO($dsn, $this->user, $this->pwd, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_STRINGIFY_FETCHES=> false
                ]);
            }
            return $this -> conn;
        }
        
    }