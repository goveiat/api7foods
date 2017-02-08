<?php

namespace Helpers;

use \Firebase\JWT\JWT;
use \Exception;
use Helpers\Login;

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

    public function getCliente($token, $db){
        $retorno = false;

        $token = $this->checkToken($token);

        if($token){
            $h = new Login;
            $d = JWT::decode($token, $_ENV[JWTFOODS], [HS256]);

            $retorno[token] = $token;

            //busca cliente
            $retorno[dados] = $db->query($h->qsAuth($d->data->user, $d->data->pass))->fetchObject();

            //busca enderecos do cliente
            $temp = $db->query($h->qsEnderecos($retorno[dados]->IDCustomer))->fetchAll();
            $retorno[enderecos] = $this->formatEnderecos($temp);
        }

        return $retorno;
    }


    public function formatEnderecos($arr){
        $retorno = [];
        foreach ($arr as $e) {
            $label = "$e[Nickname] - $e[Address], $e[Address2], $e[Number] - $e[City]";
            $key = "$e[Address2], $e[City] - $e[State]";
            $retorno[] = [id => $e[IDAddress], label => $label, key => $key];
        }

        return $retorno;
    }

}