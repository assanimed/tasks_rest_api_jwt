<?php
    declare(strict_types=1);
    include_once dirname(__DIR__). "/rest_api/vendor/autoload.php";
    set_exception_handler('ErrorHandler::handleException');
    set_error_handler('ErrorHandler::handleError');
    header("Content-Type: Application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    
    use App\DotEnv\DotEnv;
    
    
    DotEnv::load(__DIR__);
    
    