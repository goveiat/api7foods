<?php

use \Firebase\JWT\JWT;

$app->get('/', function () {
    return $this->response->withJson(['index'=> 'api7foods']);
});

/**
* @empresa
**/
$app->get('/empresa/[{host}]', function ($request, $response, $args) {
    $h = $this->Empresa;
    $retorno = [];

    $empresa = $this->db->query($h->qsDados($args['host']))->fetchObject();

    if($empresa){
        $retorno['empresa'] = $empresa;
        $id = $empresa->IDCompany;

        //Endereços
        $retorno['enderecos']= $this->db->query($h->qsEnderecos($id))->fetchAll();

        //Horario Func
        $temp = $this->db->query($h->qsHorario($id, 'businesshours'))->fetchAll();
        $retorno['h_funcionamento'] = $h->frmHorario($temp);

        //Horario Entrega
        $temp = $this->db->query($h->qsHorario($id, 'openinghours'))->fetchAll();
        $retorno['h_entrega'] = $h->frmHorario($temp);

        //Formas de pagamento
        $retorno['tipo_pagamento'] = $this->db->query($h->qsPagamentos($id))->fetchAll();
    }


    $this->Utils->checkToken($retorno, $request->getHeader('Authorization')[0]);

    return $this->response->withJson($retorno);
});


/**
* @produtos
**/
$app->get('/empresa/{id}/produtos', function ($request, $response, $args) {
    $h = $this->Produtos;

    $retorno = [];

    $id = $args['id'];

    //Produtos
    $temp = $this->db->query($h->qsProdutos($id))->fetchAll();
    $retorno['categorias'] = $h->frmProdutos($temp);

    //Tamanhos
    $temp = $this->db->query($h->qsTamanhos($id))->fetchAll();
    $retorno['tamanhos'] = $h->frmTamanhos($temp);

    //Variedades
    $temp = $this->db->query($h->qsVariedades($id))->fetchAll();
    $retorno['variedades'] = $h->frmVariedades($temp, $retorno['tamanhos']);

    //Opções
    $temp = $this->db->query($h->qsOpcoes($id))->fetchAll();
    $retorno['opcoes'] = $h->frmOpcoes($temp);

    return $this->response->withJson($retorno);
});

/**
* @login
**/
$app->post('/login', function ($request, $response) {
    $h = $this->Login;

    $retorno = [];

    $cred = $request->getParsedBody();

    $dados = $this->db->query($h->qsAuth($cred['user'], $cred['password']))->fetchObject();

    if($dados){
        $token = $this->Utils->newToken();
        return $this->response->withJson(["dados"=>$dados, "jwt"=>$token]);
    }else{
        return $this->response->withStatus(403);
    }
});


/**
* @conta
**/
$app->get('/conta', function ($request, $response) {
    return $this->response->withJson($request->getAttribute("token"));
});
