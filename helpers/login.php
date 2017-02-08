<?php

namespace Helpers;

class Login {

    public function __construct(){

    }

    public function qsAuth($email, $pw){
        return " SELECT * from customer where  Email = '$email' and Password = '$pw'";
    }


    public function qsEnderecos($id){
        return "
            SELECT IDAddress, Address, Address2, Address3, Number, City, SEOCity, Telephone, MapLat, MapLong, Nickname, State
            from address
            where Entity = 'customer' and IDEntity = $id and Status = '1'";
    }

}