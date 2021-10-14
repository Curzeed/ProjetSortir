<?php

namespace App\Services;

class Services
{

    public function __construct()
    {

    }

    function verifSiUserEstInscrit($listeUser, $user){
            foreach ($listeUser as $participants) {
                if ($user == $participants->getId()) {
                    return true;
                }
            } return false;
    }


}