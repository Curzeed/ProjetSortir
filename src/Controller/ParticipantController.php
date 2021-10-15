<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModifMotDePasseType;
use App\Form\ParticipantModifType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("ROLE_USER")
     * @Route ("/monProfil/{id}" , name="profil_participant")
     */
    public function modifierMonProfil(EntityManagerInterface $em, $id, Request $request, ParticipantRepository $pr): Response
    {
        //Récupere un objet Participant
        $profilConnecte = $pr->findOneBy(["id" => $id]);
        //lui dit que son profil est actif
        $profilConnecte->setActif(true);
        //lu idit que son password est son password
        $profilConnecte->setPassword($profilConnecte->getPassword());
        //Création d'une variable pour recupérer les informations du formulaire lié à ce controller
        $formModif = $this->createForm(ParticipantModifType::class, $profilConnecte);
        //Permet de modifier le formaulaire
        $formModif->handleRequest($request);
        //S'il est envoyé et valide alors....
        if ($formModif->isSubmitted() && $formModif->isValid()) {
            //....Persist = récupère les infos
            $em->persist($profilConnecte);
            //....Flush = envoie les infos
            $em->flush();
            // Une fois fait, faire un "redirectToRoute"afin de l'envoyer à une page "souhaité"
            return $this->redirectToRoute('app_logout');
        }
        $infos = $pr->findBy(["id" => $id]);
        return $this->renderForm('participant/infos.html.twig',
            compact('infos', 'formModif'));
    }

    /**
     * @Route ("/modifierMotDePasse" , name="modifier_mot_de_passe")
     */
    public function modifierMotDePasse(EntityManagerInterface $em, Request $request, ParticipantRepository $pr): Response
    {
        $newMotDePasse = new Participant();
        $formModifMotDePasse = $this->createForm(ModifMotDePasseType::class, $newMotDePasse);
        $formModifMotDePasse->handleRequest($request);
        if ($formModifMotDePasse->isSubmitted() && $formModifMotDePasse->isValid()) {
            $em->persist($newMotDePasse);
            $em->flush();
            $infos = $pr->findBy(["password" => $newMotDePasse]);
            return $this->render('participant/modifierMotDePasse.html.twig',
                compact('formModifMotDePasse'));
        }
        $infos = $pr->findBy(["password" => $newMotDePasse]);
        return $this->render('participant/modifierMotDePasse.html.twig',
            compact('formModifMotDePasse'));
    }


}