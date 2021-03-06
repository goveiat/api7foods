<?php

namespace Helpers;

class Empresa {

    public function __construct(){

    }

    public function qsDados($host){
        return "
            SELECT c.IDCompany, c.Name, c.Background, c.SEOName, c.Description, c.Email, i.URL as Logo
            from company as c
            inner join image as i on i.ID = c.IDCompany and i.Type = 'logo'
            where  c.Username like '%$host%'";
    }

    public function qsEnderecos($id){
        return "
            SELECT Address, Address2, Address3, Number, City, SEOCity, Telephone, MapLat, MapLong, Nickname, State
            from address
            where Entity = 'company' and IDEntity = $id  and Status = '1'";
    }

    public function qsPagamentos($id){
        return "
            SELECT t.Icon, t.Title, t.IDPaymenttype
            from paymenttype as t
            inner join companypaymenttype as c on t.IDPaymenttype = c.IDPaymenttype
            where c.IDCompany = $id ";
    }

    public function qsHorario($id, $horario){
        return "SELECT Day, Opens, Closes from $horario where IDShop = $id ";
    }

    public function qsRegioes($id){
        return "SELECT IDAddress, ValueDelivery, Cidade, Bairro, Estado from companydeliveryarea where IDCompany = $id ";
    }

    public function frmHorario($horario){
        $ret = [];
        for ($i=1; $i < 8; $i++) {
            $horas = [];
            foreach ($horario as $h) {
                if (strpos($h[Day], (string)$i) !== false) {
                    $horas[] = [Opens=> $h[Opens], Closes=>$h[Closes]];
                }
            }
            $ret[$i] = $horas;
        }
        return $ret;
    }

    public function frmRegioes($regioes){
        $ret = [];
        foreach ($regioes as $r) {
            $ret['autocomplete']["$r[Bairro], $r[Cidade] - $r[Estado]"] = null;
            $ret['valor']["$r[Bairro], $r[Cidade] - $r[Estado]"] = $r[ValueDelivery];
        }

        return $ret;
    }

}