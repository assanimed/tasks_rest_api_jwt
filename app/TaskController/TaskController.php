<?php 

    namespace App\TaskController;

    use App\TaskGateway\TaskGateway;

    class TaskController {



        public function __construct(private TaskGateway $gateWay, private int $user_id){
            
        }
        public function processRequest(string $method, ?string $id): void
        {
            if($id === null)
            {
                if($method === "POST"){
                    
                    $data =  json_decode(file_get_contents("php://input"), true) ?? [];

                    

                    $err = $this->getValidationError($data);
                    if(!empty($err)) {
                        $this -> respondUnprocessableEntity($err);
                        return;
                    }

                    $id =  $this->gateWay->createForUser($this -> user_id, $data);
                    $this -> respondCreated($id);

                } elseif($method === "GET"){
                    echo json_encode( $this -> gateWay -> getAllForUser($this->user_id));
                } else {
                    $this -> responseMethodNotAllowed("POST, GET");
                    echo json_encode(["message" => "Invalid Request"]);
                }

            } else{
                $task = $this -> gateWay -> getForUser($this->user_id, $id);
                if($task === false) {
                    $this->respondNotFount($id);
                    return;
                }  
                switch($method){
                    case "GET":
                        echo json_encode($task);
                        break;
                    case "PATCH":
                        $data =  json_decode(file_get_contents("php://input"), true) ?? [];

                        if(empty($data)) 
                        {
                            echo json_encode(["messahe" => "no Entries"]);
                            return;
                        }

                        $err = $this->getValidationError($data, false);
                        if(!empty($err)) {
                            $this -> respondUnprocessableEntity($err);
                            return;
                        }


                        foreach($task as $key => $value)
                        {
                            $task["$key"] = $data[$key] ?? $value;
                        }
                        if(array_key_exists("priority", $data) && $data["priority"] === null ) $task["priority"] = null;
                        
                        $this -> gateWay -> updateForUser($this->user_id, $id, $task);

                        $this -> respondUpdated($task);
                        break;
                    case "DELETE":
                        $rows = $this -> gateWay -> deleteForUser($this->user_id, $id);
                        echo json_encode([
                            "message" => "Task Deleted",
                            "rows" => $rows
                        ]);
                        break;
                    default:
                        $this -> responseMethodNotAllowed("GET, PATCH, DELETE");
                }
            }

        }

        private function DeleteTask($id){
            echo "DELETE TASK WITH ID {$id}";
        }
        public function setHeader(string $header): void
        {
            Header($header);
        }
        public function setResponseCode(int $code): void
        {
            http_response_code($code);
        }
        private function responseMethodNotAllowed(string $allowed_methods): void {
                    Header("Allow: $allowed_methods");
                    http_response_code(405);
                    echo json_encode(["message" => "Invalid Request"]);
        }

        private function respondNotFount(string $id): void{
            http_response_code(404);
            echo json_encode(["messag" => "Task Not Found with ID: $id!"]);
        }

        private function respondCreated(string $id): void {
            http_response_code(201);
            echo json_encode(["message" => "Resource Created", "id" => $id]);
        }

        private function getValidationError(array $data, bool $is_new = true): array {
            $errors = [];

            if($is_new && empty($data["name"]))
                $errors[] = "Name is required";
            
            if(!empty($data['priority']) &&  !filter_var($data['priority'], FILTER_VALIDATE_INT))
                $errors[] = "Priority must be an integer";
            
            return $errors;
        }

        private function respondUnprocessableEntity(array $errors): void {
            http_response_code(422);
            echo json_encode([
                "message" => "Unprocessable Entity",
                "Errors" => $errors
            ]);
        }

        private function respondUpdated(array $task): void {
            echo json_encode([
                "message" => "Task Updated",
                "Task" => $task
            ]);
        } 
    }