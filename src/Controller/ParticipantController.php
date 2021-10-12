<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantModifType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function modifierMonProfil(EntityManagerInterface $em, $id, Request $request, ParticipantRepository $pr): Response
    {
        //Instantiaton d'un nouveau participant
        $newParticipant = new Participant();
        //Création d'une variable pour recupérer les informations du formulaire lié à ce controller
        $formModif = $this->createForm(ParticipantModifType::class,$newParticipant);
        $formModif -> handleRequest($request);
        //Permet de modifier le formaulaire
        if($formModif->isSubmitted() && $formModif->isValid()){
            //Persist = récupère les infos
            $em->persist($newParticipant);
            //Flush = envoie les infos
            $em->flush();
            // Une fois fait, faire un "redirectToRoute"afin de l'envoyer à une page "souhaité"
            return $this->redirectToRoute('app_logout');
        }
        $infos = $pr->findBy(["id"=>$id]);
        return $this->renderForm('participant/infos.html.twig',compact('infos','formModif'));
    }

}

/**
//$devSession  = $dr->findOneBy(['pseudo' => $this->getUser()->getUserIdentifier()]);
*/