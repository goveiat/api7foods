<?php

namespace Helpers;

use \Firebase\JWT\JWT;
use \Exception;

class Utils{

    public function __construct(){

    }

    public function newToken($dados = []){
        return JWT::encode(
                [iat=> strtotime("now"), exp=>strtotime("+5 minutes"), iss=>"$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", data => $dados],
                $_ENV[JWTFOODS]
            );
    }


    public function checkToken($token){
        try{
            $d = JWT::decode($token, $_ENV[JWTFOODS], [HS256]);
            $retorno =  $this->newToken($d->data);
        }catch(Exception $e){
            $retorno = false;
        }

        return $retorno;
    }


}