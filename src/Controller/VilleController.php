<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    #[Route('/ville', name: 'ville')]
    public function index(): Response
    {
        return $this->render('ville/index.html.twig', [
            'controller_name' => 'VilleController',
        ]);
    }

    /**
     * @Route ("/villes/api", name="api_ville")
     */
    public function apiVille(VilleRepository $vr){
        $liste = $vr->findAll();
        $tab = [];
        foreach ($liste as $ville){
            $info['nom'] = $ville->getNom();
            $info['id'] = $ville->getId();
            $tab[]= $info;
        }
        return $this->json($tab);
    }
}
