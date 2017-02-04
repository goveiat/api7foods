<?php

class Login {

    public function __construct(){

    }

    public function qsAuth($email, $pw){
        return " SELECT * from customer where  Email = '$email' and Password = MD5('$pw')";
    }

}