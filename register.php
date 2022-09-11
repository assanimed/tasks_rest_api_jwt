<?php

require __DIR__ . "/vendor/autoload.php";
    use App\Task\TaskController;
    use App\DataBase\Database;
    use App\DotEnv\DotEnv;
    use App\TaskGateway\TaskGateway;



if ($_SERVER["REQUEST_METHOD"] === "POST") {

    


    DotEnv::load(__DIR__);

    $host=$_ENV['DB_HOST'];
    $dbname=$_ENV['DB_NAME'];
    $port=$_ENV['DB_PORT'];
    $user=$_ENV['DB_USER'];
    $pwd=$_ENV['DB_PWD'];

    
    
    $database = new Database(
        $host, $dbname,$port,$user,$pwd
    );
                             
    $conn = $database->getConnection();
    
    $sql = "INSERT INTO user (name, username, password_hash, api_key)
            VALUES (:name, :username, :password_hash, :api_key)";
            
    $stmt = $conn->prepare($sql);
    
    $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $api_key = bin2hex(random_bytes(16));
    
    $stmt->bindValue(":name", $_POST["name"], PDO::PARAM_STR);
    $stmt->bindValue(":username", $_POST["username"], PDO::PARAM_STR);
    $stmt->bindValue(":password_hash", $password_hash, PDO::PARAM_STR);
    $stmt->bindValue(":api_key", $api_key, PDO::PARAM_STR);
    
    $stmt->execute();
    
    echo "Thank you for registering. Your API key is ", $api_key;
    exit;
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <!-- <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css"> -->
</head>

<body>

    <main class="container">


        <h1>Register</h1>

        <form method="post">

            <div>
                <label for="name">
                    Name
                    <input name="name" id="name">
                </label>
            </div>

            <div>
                <label for="username">
                    Username
                    <input name="username" id="username">
                </label>
            </div>

            <div>
                <label for="password">
                    Password
                    <input type="password" name="password" id="password">
                </label>
            </div>

            <button>Register</button>
        </form>

    </main>

</body>

</html>