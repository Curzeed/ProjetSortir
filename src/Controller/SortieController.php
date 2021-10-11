<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sorties', name: 'liste_sorties')]
    public function index(SortieRepository $sr): Response
    {
        $listeSortie = $sr->findAll();
        return $this->render('sortie/index.html.twig', compact('listeSortie'));
    }

    /**
     * @Route ("/sorties/filter", name="liste_sorties_filtree")
     */
    public function listeSortieFilter(SortieRepository $sr) : Response{

        $listeSortie = $sr->findWithCampus();
        return $this->render('sortie/index.html.twig',compact('listeSortie'));
    }
}
