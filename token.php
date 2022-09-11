<?php 

$payload = [
    "sub" => $user["id"],
    "name" => $user["name"],
    "exp" => time() + 300
];

$access_token = $codec -> encode($payload);
$refresh_token_exp = time() + (60 * 60 * 24 * 5);

$refresh_token = $codec -> encode([
    "sub" => $user["id"],
    "exp" => $refresh_token_exp
]);


echo json_encode( [
    "access_token" => $access_token,
    "refresh_token" => $refresh_token,
] );