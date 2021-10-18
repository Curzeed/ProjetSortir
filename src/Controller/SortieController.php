<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Cassandra\Date;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Services;
use Symfony\Component\Validator\Constraints\DateTime;

class SortieController extends AbstractController
{
    #[Route('/sorties', name: 'liste_sorties')]
    #[IsGranted('ROLE_USER')]
    public function index(SortieRepository $sr, CampusRepository $cr): Response
    {

        $listeCampus = $cr->findAll();
        $listeSortie = $sr->findAll();
        //dd($listeSortie);
        return $this->render('sortie/index.html.twig', compact('listeSortie', 'listeCampus'));
    }

    /**
     *
     * @Route ("/sorties/filter", name="liste_sorties_filtree")
     * @IsGranted("ROLE_USER")
     */
    public function listeSortieFilter(SortieRepository $sr) : Response{

        return $this->render('sortie/index.html.twig');
    }

    /**
     * @Route ("/sorties/nouvelle/", name="sortie_nouvelle")
     * @IsGranted("ROLE_USER")
     */
    public function ajouterSortie(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $pr, LieuRepository $lr)
    {
        $sortie = new Sortie();
        $user = $this->getUser();
        $sortie->setOrganisateur($user);
        $sortie->setCampus($user->getCampus());
        $formSortie = $this->createForm(SortieType::class, $sortie);

        $formSortie->handleRequest($request);

        if($formSortie->isSubmitted() && $formSortie->isValid()){
            $lieuId = $request->get('lieu');
            $lieu = $lr->find($lieuId);
            $sortie->setLieu($lieu);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('liste_sorties');
        }
        return $this->renderForm('sortie/nouvelle.html.twig', compact('formSortie'));
    }
    /**
     * @Route("/sorties/detail/{id}", name="sortie_details")
     */
    public function afficherSortie(SortieRepository $sr, $id) : Response{
        $sortie = $sr->findOneBy(['id'=>$id]);
        return $this->render('sortie/affiche_sortie.html.twig', compact('sortie'));

    }
    /**
     * @Route("/sorties/inscription/{id}", name="sortie_inscription")
     */
    public function addInscriptionSortie(  Sortie $sortie, EntityManagerInterface $em): Response{

        $user = $this->getUser();
        $tabEtat = array("Clôturée","Passée","Annulée");
        $etatActuelSortie = $sortie->getEtat()->getLibelle();
        if(in_array($etatActuelSortie,$tabEtat) ){
            $this->addFlash('notice',
                "Vous ne pouvez pas vous inscrire à cette sortie car elle est ". $sortie->getEtat()->getLibelle());
            return $this->redirectToRoute('liste_sorties');
        }else{
            $this->addFlash('success', "
            Votre inscription à bien été prise en compte
            ");
            $sortie->addParticipantsInscrit($user);
            $em->persist($sortie);
            $em->flush();
        }


        return $this->redirectToRoute('liste_sorties');
    }
    /**
     * @Route("/sorties/desister/{id}", name="sortie_desistement")
     */
    public function removeInscription(Sortie $sortie, EntityManagerInterface $em): Response{
        $user = $this->getUser();
        $sortie->removeParticipantsInscrit($user);
        $em->flush();
        $this->addFlash('success',"Vous avez été désinscrit avec succès ! ");
        return $this->redirectToRoute('liste_sorties');
    }
    /**
     * @Route ("/api/sortie", name="api_sorties")
     */
    public function apiSorties(SortieRepository $sr, Services $s){
        $liste = $sr->findAll();
        $tab = [];

        foreach ($liste as $sortie){
            $sortie = $s->verifSiDateEstPassee($sortie);
            $userParticipant = false;
            $estOrganisateur = $s->verifSiOrganisateur($sortie, $this->getUser());
            $userParticipant = $s->verifSiUserEstInscrit($sortie->getParticipantsInscrits(), $this->getUser()->getId());
            $info['nom'] = $sortie->getNom();
            $info['dateHeureDebut'] = $sortie->getDateHeureDebut()->format('d/m/Y H:i');
            $info['duree'] = $sortie->getDuree();
            $info['dateLimiteInscription'] = $sortie->getDateLimiteInscription()->format('d/m/Y H:i');
            $info['nbInscriptionsMax'] = $sortie->getNbInscriptionsMax();
            $info['infosSortie'] = $sortie->getInfosSortie();
            $info['etat'] = $sortie->getEtat()->getLibelle();
            $info['organisateur'] = $sortie->getOrganisateur()->getNom();
            $info['EstOrganisateur'] = $estOrganisateur;
            $info['id'] = $sortie->getId();
            $info['nbParticipantsInscrits'] = count($sortie->getParticipantsInscrits());
            $info['siteOrga'] = $sortie->getCampus()->getNom();
            $info['idcampus'] = $sortie->getCampus()->getId();
            $info['userInscrit'] = $userParticipant;

            $tab []= $info;

        }
        return $this->json($tab);
    }
    /**
     * @Route("/sorties/modifier/{id}", name="sorties_modifier")
     */
    public function modifier(Sortie $sortie, SortieRepository $sr, Services $s, Request $request, LieuRepository $lr, EntityManagerInterface $entityManager){
        $user = $this->getUser();
        $isOrga = $s->verifSiOrganisateur($sortie, $user);
        if ($isOrga == true ){
            $formSortie = $this->createForm(SortieType::class, $sortie);
            $formSortie->handleRequest($request);

            if($formSortie->isSubmitted() && $formSortie->isValid()){
                $lieuId = $request->get('lieu');
                $lieu = $lr->find($lieuId);
                $sortie->setLieu($lieu);
                $entityManager->persist($sortie);
                $entityManager->flush();
                return $this->redirectToRoute('liste_sorties',compact('sortie'));
            }return $this->renderForm('sortie/modifier.html.twig',compact('sortie', 'formSortie'));

        }else{
            $this->addFlash('error',"Vous n'êtes pas l'organisateur de cette sortie donc vous ne pouvez pas la modifier");
            return $this->render('sortie/index.html.twig');
        }
    }

    /**
     * @Route("/sorties/annuler/{id}", name="sorties_annuler")
     */
    public function annulerSortie(SortieRepository $sr,$id, Services $s){
        $user = $this->getUser();
        $sortie = $sr->find($id);
        $isOrga = $s->verifSiOrganisateur($sortie, $user);
        if($isOrga == true){
            $sortie->setInfosSortie();
        }
    }

}
