<?php
    namespace App\DotEnv;

    class DotEnv {
        public static function load(string $dirname): void{
            $workDir = $dirname;
            $file = fopen($workDir. "/.env", 'r');
            $envArr = [];

            while($line = fgets($file))
            {
                $pairs = explode("=", trim($line));
                $envArr[$pairs[0]] = str_replace('"', "", $pairs[1]);
            }

            $_ENV = $envArr;
        }
    }