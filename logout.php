<?php
    declare(strict_types=1);

    use App\UserGateway\UserGateway;
    use App\DataBase\Database;
    use App\JWT\JWT;
    use App\RefreshTokenGateway\RefreshTokenGateway;

    require_once __DIR__."/__bootstrap.php";




    if($_SERVER["REQUEST_METHOD"] !== "POST")
    {
        http_response_code(405);
        header("Allow: POST");
        exit;
    }

    $data = (array) json_decode(file_get_contents("php://input"), true);

    if(!array_key_exists("token", $data)) {
        http_response_code(400);
        echo json_encode([
            "message" => "Missing Token"
        ]);
        exit;
    }

    $codec = new JWT($_ENV["HASH_KEY"]);


    try {
        $payload = $codec -> decode($data["token"]);
    } catch (Exception) {
        http_response_code(400);
        echo json_encode([
            "message" => "Invalid Token"
        ]);
        exit;
    }



    $db = new Database(
        $_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PWD']
    );

    $refresh_token_gateway = new RefreshTokenGateway($db, $_ENV['HASH_KEY']);
    
    $refresh_token_gateway -> delete($data["token"]);