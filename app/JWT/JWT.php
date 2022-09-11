<?php

    namespace App\JWT;

    use App\ExpiredTokenException\ExpiredTokenException;
    use Exception;
    use App\InvalidSignatureExceptions\InvalidSignatureExceptions;
    use InvalidArgumentException;

    class JWT {
        public function __construct(private string $key){}
        
        public function encode (array $payload): string {
            $header = json_encode([
                "typ" => "JWT",
                "alg" => "HS256"
            ]);
            $header =  $this-> base64urlEncode($header);

            $payload = json_encode($payload);
            $payload = $this -> base64urlEncode($payload);

            $signature = hash_hmac("sha256", 
                                    $header . ".".$payload,
                                    $this -> key,
                                    true);
            $signature = $this -> base64urlEncode($signature);

            

            return $header . "." . $payload . "." . $signature;
        }


        public function decode(string $token): array {
            if(preg_match(
                            "/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/",
                            $token, 
                            $matches) !== 1)
                {
                    throw new InvalidArgumentException("Invalid Token Format");
                }
            
            $signature = hash_hmac("sha256", 
                                    $matches["header"] . ".".$matches["payload"],
                                    $this -> key,
                                    true);

            $token_signature = $this -> base64urlDecode($matches["signature"]);

            if(!hash_equals($signature, $token_signature)){
                throw new InvalidSignatureExceptions;
            }

            $payload = json_decode($this->base64urlDecode($matches["payload"]), true);


            if($payload["exp"] < time()){
                throw new ExpiredTokenException("Token Expired");
            }


            return $payload;
        }

        private function base64urlEncode(string $text):string {

            $text = base64_encode($text);
            return str_replace(
                ["+","/","="],["-","_",""], $text
            );
        }

        private function base64urlDecode(string $text):string {

            $text = str_replace(
                ['-',"_"], ['+','/'], $text
            );

            return base64_decode($text);
        }


    }