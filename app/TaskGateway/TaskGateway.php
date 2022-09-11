<?php 

    namespace App\TaskGateway;

    use App\DataBase\Database;

    class TaskGateway {
        private \PDO $conn;
        public function __construct(Database $databse){
            $this -> conn = $databse -> getConnection();
        }

        public function getAllForUser(int $user_id): array {
            $sql = "SELECT * FROM task WHERE user_id=:u_id ORDER By id;";
            $stmt = $this -> conn -> prepare($sql);
            $stmt -> bindValue(":u_id", $user_id, \PDO::PARAM_INT);
            $stmt -> execute();

            $data = [];
            while($row = $stmt -> fetch(\PDO::FETCH_ASSOC))
            {
                $row["is_completed"] = (bool) $row["is_completed"];
                $data[] = $row;
            }
            return $data;
        }

        public function getForUser(int $user_id, string $id): array | false {
            $sql = "SELECT * FROM `task` WHERE id=:id AND user_id=:u_id LIMIT 1;";

            $stmt = $this -> conn -> prepare($sql);
            $stmt -> bindValue(":id", $id, \PDO::PARAM_INT);
            $stmt -> bindValue(":u_id", $user_id, \PDO::PARAM_INT);
            $stmt -> execute();

            $data = $stmt -> fetch(\PDO::FETCH_ASSOC);

            if($data){
                $data["is_completed"] = (bool) $data["is_completed"];
            }
            
            return $data;
        }

        public function createForUser(int $user_id, array $data) : string
        {
            $sql = "INSERT INTO task(name,priority, is_completed, user_id) VALUES(:name,:prio,:comp, :u_id);";

            $stmt = $this -> conn -> prepare($sql);
            $stmt -> bindValue(":name", $data['name'], \PDO::PARAM_STR);
            
            if(empty($data['priority']))
                $stmt -> bindValue(":prio", null, \PDO::PARAM_NULL);
            else
                $stmt -> bindValue(":prio", $data['priority'], \PDO::PARAM_INT);

            $stmt -> bindValue(":comp", $data['is_completed'] ?? false, \PDO::PARAM_BOOL);
            $stmt -> bindValue(":u_id", $user_id, \PDO::PARAM_INT);
            $stmt -> execute();

            return $this -> conn -> lastInsertId();
        }

        public function deleteForUser(int $user_id, string $id): int {
            $sql = "DELETE FROM `task` WHERE id=:id AND user_id=:u_id;";

            $stmt = $this -> conn -> prepare($sql);
            $stmt -> bindValue(":id", $id, \PDO::PARAM_INT);
            $stmt -> bindValue(":u_id", $user_id, \PDO::PARAM_INT);
            $stmt -> execute();

            return $stmt -> rowCount();
        }

        public function updateForUser(int $user_id, string $id, array $data): void
        {

            $sql = "UPDATE task 
            SET name=:name, is_completed=:comp,priority=:prio
            WHERE id=:id AND user_id=:u_id;";

            $stmt = $this -> conn -> prepare($sql);
            $stmt ->bindValue(':id', $id, \PDO::PARAM_STR);
            $stmt ->bindValue(':name', $data['name'], \PDO::PARAM_STR);
            if($data['priority'] === null )
                $stmt ->bindValue(':prio', $data['priority'], \PDO::PARAM_NULL);
            else
                $stmt ->bindValue(':prio', $data['priority'], \PDO::PARAM_INT);
            $stmt ->bindValue(':comp', $data['is_completed'], \PDO::PARAM_BOOL);
            $stmt ->bindValue(':u_id', $user_id, \PDO::PARAM_INT);

            $stmt -> execute();
        }
    }