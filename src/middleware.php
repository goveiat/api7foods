<?php
// Obtém dados do ambiente
$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

//Inclui o middleware de autenticação
$app->add(new \Slim\Middleware\JwtAuthentication([
    "secure" => false,
    "path" => ['/conta', '/comprar'],
    "secret" => $_ENV['JWTFOODS'],
    "algorithm" => "HS256",
    "callback" => function ($request, $response, $args){
        exit('oi');
    }
]));
