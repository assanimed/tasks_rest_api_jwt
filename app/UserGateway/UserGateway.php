<?php 

    namespace App\UserGateway;
    use App\DataBase\Database;

    class UserGateway {
        private \PDO $conn;

        public function __construct(Database $db)
        {
            $this -> conn = $db -> getConnection();
        }

        public function getByAPIkey(string $api): array | false 
        {
            $sql = "SELECT * FROM `user` WHERE api_key=:key";
            $stmt = $this -> conn -> prepare($sql);
            $stmt -> bindValue(":key", $api, \PDO::PARAM_STR);
            $stmt -> execute();

            $rowData = $stmt -> fetch(\PDO::FETCH_ASSOC);

            return $rowData;
        }

        public function getByUsername(string $username) : array | false
        {
            
            $sql = "SELECT * FROM `user` WHERE username=:username";
            
            $stmt = $this -> conn -> prepare($sql);
            
            $stmt -> bindValue(":username", $username, \PDO::PARAM_STR);
            
            $stmt -> execute();

            $rowData = $stmt -> fetch(\PDO::FETCH_ASSOC);

            return $rowData;
            
        }

        public function getBtyId(int $id): array | false {

            $sql = "SELECT * from user WHERE id=:id;";

            $stmt = $this->conn -> prepare($sql);
            $stmt -> bindValue(":id", $id, \PDO::PARAM_INT);
            $stmt -> execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

    }