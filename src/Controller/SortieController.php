<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Services;

class SortieController extends AbstractController
{
    #[Route('/sorties', name: 'liste_sorties')]
    #[IsGranted('ROLE_USER')]
    public function index(SortieRepository $sr, CampusRepository $cr): Response
    {

        $listeCampus = $cr->findAll();
        $listeSortie = $sr->findAll();
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
     * @Route ("/sorties/nouvelle/{pseudo}", name="sortie_nouvelle")
     * @IsGranted("ROLE_USER")
     */
    public function ajouterSortie(Request $request, EntityManagerInterface $entityManager, $pseudo, ParticipantRepository $pr, LieuRepository $lr)
    {
        $sortie = new Sortie();
        $test = $pr->findOneBy(["username"=>$pseudo]);
        $sortie->setOrganisateur($test);
        $sortie->setCampus($test->getCampus());
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
        $sortie->addParticipantsInscrit($user);
        $em->flush();

        return $this->redirectToRoute('liste_sorties');
    }
    /**
     * @Route("/sorties/desister/{id}", name="sortie_desistement")
     */
    public function removeInscription(Sortie $sortie, EntityManagerInterface $em): Response{
        $user = $this->getUser();
        $sortie->removeParticipantsInscrit($user);
        $em->flush();
        return $this->redirectToRoute('liste_sorties');
    }
    /**
     * @Route ("/api/sortie", name="api_sorties")
     */
    public function apiSorties(SortieRepository $sr, Services $s){
        $liste = $sr->findAll();
        $tab = [];

        foreach ($liste as $sortie){
            $userParticipant = false;
            $userParticipant = $s->verifSiUserEstInscrit($sortie->getParticipantsInscrits(), $this->getUser()->getId());
            $info['nom'] = $sortie->getNom();
            $info['dateHeureDebut'] = $sortie->getDateHeureDebut();
            $info['duree'] = $sortie->getDuree();
            $info['dateLimiteInscription'] = $sortie->getDateLimiteInscription();
            $info['nbInscriptionsMax'] = $sortie->getNbInscriptionsMax();
            $info['infosSortie'] = $sortie->getInfosSortie();
            $info['etat'] = $sortie->getEtat()->getLibelle();
            $info['organisateur'] = $sortie->getOrganisateur()->getNom();
            $info['id'] = $sortie->getId();
            $info['nbParticipantsInscrits'] = count($sortie->getParticipantsInscrits());
            $info['siteOrga'] = $sortie->getCampus()->getNom();
            $info['userInscrit'] = $userParticipant;
            $tab []= $info;
        }
        return $this->json($tab);
    }
}
