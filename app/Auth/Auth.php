<?php
    namespace App\Auth;

use App\ExpiredTokenException\ExpiredTokenException;
use App\InvalidSignatureExceptions\InvalidSignatureExceptions;
use App\JWT\JWT;
    use App\UserGateway\UserGateway;

    class Auth {
        private int $user_id;
        public function __construct(private UserGateway $userGateway,
                                    private JWT $codec){}
        
        
        public function authenticateApiKey() : bool
        {

            if(empty($_SERVER["HTTP_X_API_KEY"]))
            {
                http_response_code(400);
                echo json_encode(["message" => "API KEY IS MISSING"]);
                return false;
            }
            $api_key =  $_SERVER["HTTP_X_API_KEY"];

            $user = $this -> userGateway -> getByAPIkey($api_key);

            if(!$user)
            {
                echo json_encode(['messagee' => "invalid API KEY"]);
                http_response_code(401);
                return false;
            }
            $this -> user_id = $user["id"];

            return true;
        }

        public function getUserId(): int 
        {
            return (int) $this -> user_id;
        }


        public function authenticateAccessToken()
        {

            if(!preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches))
            {
                http_response_code(400);
                echo json_encode(["message" => "Incomplete authorization Header"]);
                return false;
            }

            try {
                $userData = $this->codec -> decode($matches[1]);
            } catch (InvalidSignatureExceptions) {
                http_response_code(401);
                echo json_encode(["message" => "Signature Doesn't match!"]);
                return false;
            }
            catch (ExpiredTokenException $err) {
                http_response_code(401);
                echo json_encode(["message" => $err->getMessage()]);
                return false;
            }

            catch( \Exception $err) {
                http_response_code(400);
                echo json_encode(["message" => $err -> getMessage()]);
                return false;
            } 


            $this -> user_id = $userData["sub"];

            return true;
        }
    }