<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    #[Route('/participant', name: 'participant')]
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    /**
     * @Route ("/monProfil/{id}" , name="profil_participant")
     */
    public function modifierMonProfil(ParticipantRepository $pr, $id): Response
    {
        $infos = $pr->findBy(["id"=>$id]);
        return $this->render('participant/infos.html.twig',compact('infos'));
    }

}

/**
//$devSession  = $dr->findOneBy(['pseudo' => $this->getUser()->getUserIdentifier()]);
*/