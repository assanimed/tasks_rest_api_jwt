<?php
    require_once __DIR__."/__bootstrap.php";
    use App\TaskController\TaskController;
    use App\DataBase\Database;
    use App\TaskGateway\TaskGateway;
    use App\UserGateway\UserGateway;
    use App\Auth\Auth;
    use App\JWT\JWT;
    

    $db = new Database(
        $_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PWD']
    );
    $task_gateway = new TaskGateway($db);
    $userGateway = new UserGateway($db);
    $codec = new JWT($_ENV["HASH_KEY"]);
    $auth = new Auth($userGateway, $codec);
    

    
    $path = explode("/", parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH));

    $resource = $path[2];
    $id = $path[3] ?? null;

    if($resource !== "tasks")
    {
        echo json_encode([
            "ErrorCode" => 404,
            "message" => "Resource Not Found"
        ]);
        http_response_code(404);
        exit();
    }


    if(!$auth -> authenticateAccessToken()) exit;

    $user_id = $auth -> getUserId();

    $controller = new TaskController($task_gateway, $user_id);

    $controller -> processRequest($_SERVER["REQUEST_METHOD"], $id);