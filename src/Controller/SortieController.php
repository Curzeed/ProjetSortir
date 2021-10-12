<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sorties', name: 'liste_sorties')]
    public function index(SortieRepository $sr, CampusRepository $cr): Response
    {

        $listeCampus = $cr->findAll();
        $listeSortie = $sr->findAll();
        return $this->render('sortie/index.html.twig', compact('listeSortie', 'listeCampus'));
    }

    /**
     * @Route ("/sorties/filter", name="liste_sorties_filtree")
     */
    public function listeSortieFilter(SortieRepository $sr) : Response{

        return $this->render('sortie/index.html.twig');
    }

    /**
     * @Route ("/sorties/nouvelle/{pseudo}", name="sortie_nouvelle")
     */
    public function ajouterSortie(Request $request, EntityManagerInterface $entityManager, $pseudo, ParticipantRepository $pr)
    {
        $sortie = new Sortie();
        $test = $pr->findOneBy(["username"=>$pseudo]);
        $sortie->setOrganisateur($test);
        $sortie->setCampus($test->getCampus());
        $formSortie = $this->createForm(SortieType::class, $sortie);

        $formSortie->handleRequest($request);

        if($formSortie->isSubmitted() && $formSortie->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('liste_sorties');
        }
        return $this->renderForm('sortie/nouvelle.html.twig', compact('formSortie'));
    }
}
