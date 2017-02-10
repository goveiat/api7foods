<?php

use \Firebase\JWT\JWT;

$app->get('/', function () {
    return $this->response->withJson(['index'=> 'api7foods']);
});


/**
* @login
**/
$app->post('/login', function ($request, $response) {
    $h = $this->Login;

    $retorno = [];

    $cred = $request->getParsedBody();

    $pass = md5($cred[password]);

    $retorno[dados] = $this->db->query($h->qsAuth($cred[user], $pass))->fetchObject();

    if($retorno[dados]){

        $temp = $this->db->query($h->qsEnderecos($retorno[dados]->IDCustomer))->fetchAll();
        $retorno[enderecos] = $this->Utils->formatEnderecos($temp);

        $data = [user => $cred[user], pass =>$pass];

        $retorno[token] = $this->Utils->newToken($data);

        return $this->response->withJson($retorno);
    }else{
        return $this->response->withStatus(403);
    }
});



/**
* @empresa
**/
$app->get('/empresa/[{host}]', function ($request, $response, $args) {
    $h = $this->Empresa;
    $retorno = [];
    $retorno[empresa] = [];
    $retorno[cliente] = [];

    $empresa = $this->db->query($h->qsDados($args['host']))->fetchObject();

    if($empresa){
        $retorno[empresa][dados] = $empresa;
        $id = $empresa->IDCompany;

        //Endereços
        $retorno[empresa][enderecos]= $this->db->query($h->qsEnderecos($id))->fetchAll();

        //Horario Func
        $temp = $this->db->query($h->qsHorario($id, 'businesshours'))->fetchAll();
        $retorno[empresa][hFuncionamento] = $h->frmHorario($temp);

        //Horario Entrega
        $temp = $this->db->query($h->qsHorario($id, 'openinghours'))->fetchAll();
        $retorno[empresa][hEntrega] = $h->frmHorario($temp);

        //Formas de pagamento
        $retorno[empresa][tipoPagamento] = $this->db->query($h->qsPagamentos($id))->fetchAll();

        //Áreas de entrega
        $temp = $this->db->query($h->qsRegioes($id))->fetchAll();
        $retorno[empresa][regioes] = $h->frmRegioes($temp);

        //verifica validade do login
        $retorno[cliente] = $this->Utils->getCliente($request->getHeader('Authorization')[0], $this->db);

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
    $ret = [];

    $id = $args[id];

    //Produtos
    $temp = $this->db->query($h->qsProdutos($id))->fetchAll();
    $ret[categorias] = $h->frmProdutos($temp);

    //Tamanhos
    $temp = $this->db->query($h->qsTamanhos($id))->fetchAll();
    $ret[tamanhos] = $h->frmTamanhos($temp);

    //Variedades
    $temp = $this->db->query($h->qsVariedades($id))->fetchAll();
    $ret[variedades] = $h->frmVariedades($temp, $ret[tamanhos]);

    //Opções
    $temp = $this->db->query($h->qsOpcoes($id))->fetchAll();
    $ret[opcoes] = $h->frmOpcoes($temp);

    $retorno[produtos] = $ret;
    $retorno[cliente] = $this->Utils->getCliente($request->getHeader('Authorization')[0], $this->db);

    return $this->response->withJson($retorno);
});


/**
* @conta
**/
$app->post('/comprar', function ($request, $response) {
    $compra = $request->getParsedBody();
    return $this->response->withJson($compra);
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
