<?php

$app->get('/', function () {
    return $this->response->withJson(array('index'=> 'api7foods'));
});

/**
* @empresa
**/
$app->get('/empresa/[{host}]', function ($request, $response, $args) {
    $h = new Empresa();

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

    return $this->response->withJson($retorno);
});


/**
* @produtos
**/
$app->get('/empresa/{id}/produtos', function ($request, $response, $args) {
    $h = new Produtos();

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
    $h = new Login();

    $retorno = [];

    $cred = $request->getParsedBody();

    $auth = $this->db->query($h->qsAuth($cred['user'], $cred['password']))->fetchObject();

    if($auth){
        return $this->response->withJson($auth);
    }else{
        return $this->response->withStatus(403);
    }
});


$app->get('/cliente', function ($request, $response) {
    return $this->response->withJson('oi');
});
