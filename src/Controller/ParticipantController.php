<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModifMotDePasseType;
use App\Form\ParticipantModifType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function modifierMonProfil(EntityManagerInterface $em,Participant $id, Request $request, ParticipantRepository $pr, SluggerInterface $slugger): Response
    {
        //Récupere un objet Participant
        $profilConnecte = $id;
        //lui dit que son profil est actif
        $profilConnecte->setActif(true);
        //lui dit que son password est son password
        $profilConnecte->setPassword($profilConnecte->getPassword());
        //Création d'une variable pour recupérer les informations du formulaire lié à ce controller
        $formModif = $this->createForm(ParticipantModifType::class, $profilConnecte);
        //Permet de modifier le formaulaire
        $formModif->handleRequest($request);
        //S'il est envoyé et valide alors....
        if ($formModif->isSubmitted() && $formModif->isValid()) {
            $image = $formModif->get('image')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->getClientOriginalExtension();
                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $photoProfil = "profil/".$newFilename;
                $profilConnecte->setImage($photoProfil);
            }
            //....Persist = récupère les infos
            $em->persist($profilConnecte);
            //....Flush = envoie les infos
            $em->flush();
            // Une fois fait, faire un "redirectToRoute"afin de l'envoyer à une page "souhaité"
            return $this->redirectToRoute('liste_sorties');
        }
        $infos = $pr->findBy(["id" => $id]);
        return $this->renderForm('participant/infos.html.twig',
            compact('infos', 'formModif'));
    }

    /**
     * @Route ("/modifierMotDePasse" , name="modifier_mot_de_passe")
     */
    public function modifierMotDePasse(Request $request,
                                       UserPasswordHasherInterface $passwordEncoder,
                                       EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $formModif = $this->createForm(ModifMotDePasseType::class);

        $formModif->handleRequest($request);
        if ($formModif->isSubmitted() && $formModif->isValid()) {
            $nouveaumdp = $formModif->getData()['newPassword'];
            $ancienMdp = $formModif->getData()['oldPassword'];
            if($passwordEncoder->isPasswordValid($user, $ancienMdp)){

                    $passwordHash = $passwordEncoder->hashPassword($user, $nouveaumdp);
                    $user->setPassword($passwordHash);
                    $em->flush();

                return $this->redirectToRoute('liste_sorties');
            }else{
                $this->addFlash('error',"Votre ancien mot de passe ne correspond pas à celui que vous avez
                rentré !");
            }
        }

        return $this->renderForm('participant/modifierMotDePasse.html.twig',compact('formModif'));
    }

        /**
         *@Route ("/participants/infos/{pseudo}" , name="participant_sortie")
         */
        public function afficherParticipantSortie( $pseudo, ParticipantRepository $pr){
 
            // On appelle le pseudo afin de recuperer les infos dans le twig"afficherParticipantSortie"
                $utilisateur = ($pr->findOneBy(['username'=>$pseudo]));
                return $this->render('sortie/afficherParticipantSortie.html.twig',
                        compact('utilisateur'));
        }

}