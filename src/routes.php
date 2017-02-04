<?php
$model = new Empresa();
$app->get('/', function () {
    return $this->response->withJson(array('index'=> 'api7foods'));
});

$app->get('/empresa/[{host}]', function ($request, $response, $args) use ($model) {
    $host = "%".$args['host']."%";
    $retorno = array();

    $d = $this->db->prepare($model->qsDados());
    $d->bindParam("host", $host);
    $d->execute();
    $empresa = $d->fetchObject();

    if($empresa){
        $retorno['empresa'] = $empresa;
        $id = $empresa->IDCompany;

        //Endereços
        $d = $this->db->prepare($model->qsEnderecos($id));
        $d->execute();
        $retorno['enderecos'] = $d->fetchAll();

        //Horario Func
        $d = $this->db->prepare($model->qsHorarios($id, 'businesshours'));
        $d->execute();
        $retorno['h_funcionamento'] = $d->fetchAll();


        //Horario Entrega
        $d = $this->db->prepare($model->qsHorarios($id, 'openinghours'));
        $d->execute();
        $retorno['h_entrega'] = $d->fetchAll();

        //Formas de pagamento
        $d = $this->db->prepare($model->qsPagamentos($id));
        $d->execute();
        $retorno['tipo_pagamento'] = $d->fetchAll();
    }

    return $this->response->withJson($retorno);
});
