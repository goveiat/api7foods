<?php

namespace Helpers;

use \Firebase\JWT\JWT;
use \Exception;

class Utils{

    public function __construct(){

    }

    public function newToken($dados = []){
        return JWT::encode(
                ["iat"=> strtotime("now"), "exp"=>strtotime("+5 minutes"), "iss"=>"$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", "dados" => $dados],
                $_ENV['JWTFOODS']
            );
    }


    public function checkToken(&$retorno, $token){
        try{
            JWT::decode($token, $_ENV['JWTFOODS'], ['HS256']);
            $retorno['login'] = true;
            $retorno['token'] = $this->newToken();
        }catch(Exception $e){
            $retorno['login'] = false;
        }
    }


}