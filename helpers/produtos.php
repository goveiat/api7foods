<?php

namespace Helpers;

class Produtos {

    public function __construct(){

    }

    public function qsProdutos($id){
        return "
        SELECT p.IDProduct, p.ISDelivery, p.IDProductCategory, p.Name, p.Description, p.Value, p.Image, p.order_id, c.Title AS Categoria
        from product AS p
        inner join category AS c on p.IDProductCategory = c.IDProductCategory
        where p.IDCompany = $id and p.Status = '1' and p.ISOnline = '1' and c.ISCombo = '0' and  c.Status = '1'
        order by Categoria asc ";
    }


    public function qsVariedades($id){
        return "
        SELECT v.IDVariety,  v.ISDelivery, v.IDProduct, v.IDSize, v.Name, v.Description, v.Value, v.Image, v.order_id, v.group_id
        from varietyproduct AS v
        inner join product AS p on p.IDProduct = v.IDProduct
        where p.IDCompany = $id and p.Status = '1' and p.ISOnline ='1' and p.ISCombo ='0' and v.Status = '1' and v.ISOnline ='1'";
    }

    public function qsTamanhos($id){
        return "
        SELECT s.IDSize, s.Name, s.Half, s.Additional
        from size AS s
        where s.IDCompany = $id and s.Status = '1'";
    }

    public function qsOpcoes($id){
        return "
        SELECT o.IDProductoption, o.Title, c.Value, c.ISRequired, c.IDProduct
        from productoption AS o
        inner join companyproductoption AS c on o.IDProductoption = c.IDProductoption
        where o.IDCompany = $id and o.Status = '1'and c.Status = '1' and c.IDCompany = $id ";
    }


    public function frmProdutos($prods){
        $produtos = [];
        foreach ($prods as $arrP) {
            $p = (object)$arrP;
            $cat = $p->Categoria;
            $k = $p->IDProductCategory;
            $produtos[$cat][id] = $k;
            $produtos[$cat][produtos][] = $p;
        }

        return $produtos;
    }

    public function frmTamanhos($tams){
        $tamanhos = [];
        foreach ($tams as $arrT) {
            $t = (object)$arrT;
            $k = $t->IDSize;
            $tamanhos[$k] = $t;
        }
        return $tamanhos;
    }

    public function frmVariedades($vars, $tamanhos){
        $variedades = [];
        foreach ($vars as $arrV) {
            $v = (object)$arrV;
            $k = $v->IDProduct;
            $k2 = $v->group_id;
            $tam = $v->IDSize;
            if(!isset($variedades[$k][$k2])){
                $variedades[$k][$k2] = $v;
                $variedades[$k][$k2]->minVal = 9999;
            }
            $variedades[$k][$k2]->tamanhos[] = array_merge([valor=>$v->Value], (array)$tamanhos[$tam]);
            if($v->Value < $variedades[$k][$k2]->minVal){
                $variedades[$k][$k2]->minVal = $v->Value;
            }
        }

        $auxVar = [];
        foreach ($variedades as $k => $v) {
            $minV = 0;
            $auxVar[$k] = array_values($v);
        }

        return $auxVar;
    }


    public function frmOpcoes($ops){
        $opcoes = [];
        foreach ($ops as $arrO) {
            $o = (object)$arrO;
            $k = $o->IDProduct;
            $opcoes[$k][] = $o;
        }

        return $opcoes;
    }
}