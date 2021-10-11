<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        $listeSortie = $sr->findWithCampus();
        return $this->render('sortie/index.html.twig',compact('listeSortie'));
    }
}
