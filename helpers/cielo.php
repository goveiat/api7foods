<?php

namespace Helpers;

use Tritoq\Payment\Cielo\Cartao;
use Tritoq\Payment\Cielo\CieloService;
use Tritoq\Payment\Cielo\Loja;
use Tritoq\Payment\Cielo\Pedido;
use Tritoq\Payment\Cielo\Portador;
use Tritoq\Payment\Cielo\Transacao;


class Cielo{

    var $portador = null;
    var $loja = null;
    var $cartao = null;
    var $transacao = null;
    var $pedido = null;
    var $service = null;

    public function __construct(){
        $this->portador = new Portador();
        $this->loja = new Loja();
        $this->cartao = new Cartao();
        $this->transacao = new Transacao();
        $this->pedido = new Pedido();
    }

    public function setPortador($bairro, $cep, $complem, $endereco){
        $this->portador
        ->setBairro($bairro)
        ->setCep($cep)
        ->setComplemento($complem)
        ->setEndereco($endereco);
    }

    public function setLoja($nome, $retorno){
        $this->loja
        ->setNomeLoja($nome)
        ->setAmbiente(Loja::AMBIENTE_TESTE)
        ->setUrlRetorno($retorno)
        ->setChave(Loja::LOJA_CHAVE_AMBIENTE_TESTE)
        ->setNumeroLoja(Loja::LOJA_NUMERO_AMBIENTE_TESTE);
        // ->setSslCertificado($_ENV['SSLCERT']);
    }

    public function setCartao($nmPortador){
        $this->cartao
        ->setNumero(Cartao::TESTE_CARTAO_NUMERO)
        ->setCodigoSegurancaCartao(Cartao::TESTE_CARTAO_CODIGO_SEGURANCA)
        ->setBandeira(Cartao::BANDEIRA_VISA)
        ->setNomePortador($nmPortador)
        ->setValidade(Cartao::TESTE_CARTAO_VALIDADE);
    }

    public function setTransacao($parcelas){
        $this->transacao
        ->setAutorizar(Transacao::AUTORIZAR_SEM_AUTENTICACAO)
        ->setCapturar(Transacao::CAPTURA_NAO)
        ->setParcelas($parcelas)
        ->setProduto(Transacao::PRODUTO_CREDITO_AVISTA);
    }


    public function setPedido($descricao, $idPedido, $valor){
        $this->pedido
        ->setDataHora(new \DateTime())
        ->setDescricao($descricao)
        ->setIdioma(Pedido::IDIOMA_PORTUGUES)
        ->setNumero($idPedido)
        ->setValor($valor);
    }

    public function getService(){
        $this->service = new CieloService(array(
            'portador' => $this->portador,
            'loja' => $this->loja,
            'cartao' => $this->cartao,
            'transacao' => $this->transacao,
            'pedido' => $this->pedido,
        ));

        $this->service->setSslVersion(3);

        return $this->service;
    }


    public function getTransacao(){
        return $this->transacao;
    }


    public function isAutorizada(){
        if($this->transacao->getStatus() == Transacao::STATUS_AUTORIZADA) {
            return true;
        }else{
            return false;
        }
    }

    public function isCapturada(){
        if($this->transacao->getStatus() == Transacao::STATUS_CAPTURADA) {
            return true;
        }else{
            return false;
        }
    }

    public function getRequisicoes(){
        return $this->transacao->getRequisicoes(Transacao::REQUISICAO_TIPO_CONSULTA);
    }



}