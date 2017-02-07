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

        //Áreas de entrega
        $temp = $this->db->query($h->qsRegioes($id))->fetchAll();
        $retorno['regioes'] = $h->frmRegioes($temp);

        $retorno['jwt'] = $this->Utils->checkToken($request->getHeader('Authorization')[0]);

        return $this->response->withJson($retorno);
    }else{
        return $this->response->withStatus(403);
    }



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

    $retorno['jwt'] = $this->Utils->checkToken($request->getHeader('Authorization')[0]);

    return $this->response->withJson($retorno);
});

/**
* @login
**/
$app->post('/login', function ($request, $response) {
    $h = $this->Login;

    $retorno = [];

    $cred = $request->getParsedBody();

    $retorno['dados'] = $this->db->query($h->qsAuth($cred['user'], $cred['password']))->fetchObject();

    if($retorno['dados']){
        $retorno['jwt'] = $this->Utils->newToken();
        return $this->response->withJson($retorno);
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



/**
* @pedido
**/
$app->get('/pedido', function ($request, $response) {
    $h = $this->Cielo;

    $h->setPortador('Vila Celeste', '35162520', 'Ap. 202', 'Rua Quirua, 53');
    $h->setLoja('Lig China', 'http://api7foods.a2/');
    $h->setCartao('Thiago Goveia');
    $h->setTransacao(1);
    $h->setPedido('Pedido Teste', 10, 500);

    $s = $h->getService();
    $t = $h->getTransacao();

    $s->doTransacao(false, false);

    if($h->isAutorizada()) {
        $s->doCaptura();
        if($h->isCapturada()) {
            $s->doConsulta();
            $r = $h->getRequisicoes();
            echo 'Status: '  . $t->getStatus() . '<br/>';

            if(isset($r[0])) {
                echo 'XML:' . $r[0]->getXmlRetorno()->asXML();
            }
        } else {
            echo 'Transação Não Capturada, Status: ' . $t->getStatus();
        }
    } else {
        echo 'Transação Não Autorizada, Status: ' . $t->getStatus();
    }
});
