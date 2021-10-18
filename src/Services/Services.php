<?php

namespace App\Services;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;

class Services
{
    private $repoEtat ;
    private $em;

    public function __construct(EtatRepository $repoEtat, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repoEtat = $repoEtat;
    }

    function verifSiUserEstInscrit($listeUser, $user){
            foreach ($listeUser as $participants) {
                if ($user == $participants->getId()) {
                    return true;
                }
            } return false;
    }
    function verifSiDateEstPassee(Sortie $sortie){
        $today = date("Y-m-d");
        $today_dt = new \DateTime($today);
        $dateSortie = $sortie->getDateLimiteInscription();
        if($sortie->getEtat()->getId() != 6){
            if ($today_dt > $dateSortie){
                $passee = $this->repoEtat->find(5);

                $sortie->setEtat($passee);
            }
            else{
                $publie = $this->repoEtat->find(2);
                $sortie->setEtat($publie);
            }
            $this->em->persist($sortie);
            $this->em->flush();
            return $sortie;
        }


    }
    function verifSiOrganisateur(Sortie $sortie, $user){

        if ($sortie->getOrganisateur()->getUserIdentifier() == $user->getUserIdentifier()){
            return true;
        }else{
            return false;
        }
    }


}