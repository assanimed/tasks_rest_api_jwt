<?php

    namespace App\RefreshTokenGateway;

    use App\DataBase\Database;


    class RefreshTokenGateway {
        private \PDO $conn;
        private string $key;
        public function __construct(Database $db, string $key)
        {
            $this -> conn = $db -> getConnection();
            $this -> key = $key;
        }


        public function create(string $token, int $exp_time): bool{

            $hash = hash_hmac("sha256", $token, $this -> key);
           

            $sql = "INSERT INTO refresh_token(token_hash, expires_at) VALUES (:tokenhash, :exp)";

            $stmt = $this -> conn -> prepare($sql);

            $stmt -> bindValue(":tokenhash", $hash, \PDO::PARAM_STR);

            $stmt -> bindValue(":exp", $exp_time, \PDO::PARAM_INT);

            return $stmt -> execute();
        }


        public function delete(string $token): int
        {
            $hash = hash_hmac("sha256", $token, $this->key);

            $sql = "DELETE FROM refresh_token
                    WHERE token_hash=:hash";
            $stmt = $this -> conn -> prepare($sql);
            
            $stmt -> bindValue(":hash", $hash, \PDO::PARAM_STR);

            $stmt -> execute();

            return $stmt -> rowCount();
        }

        public function getByToken(string $token): array | false {
            

            $hash = hash_hmac("sha256", $token, $this -> key);

            $sql = "SELECT * FROM refresh_token 
                    WHERE token_hash=:hashToken;";
            
            $stmt = $this -> conn -> prepare($sql);

            $stmt -> bindValue(":hashToken", $hash, \PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch(\PDO::FETCH_ASSOC);
        }
    }