<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    #[Route('/lieu', name: 'lieu')]
    public function index(): Response
    {
        return $this->render('lieu/index.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }
    /**
     * @Route ("/api_lieu", name="api_lieu")
     */
    public function apiLieu(LieuRepository $lr){
        $liste = $lr->findAll();
        $tab = [];
        foreach ($liste as $lieu){
            $info['nom'] = $lieu->getNom();
            $info['id'] = $lieu->getId();
            $info['ville'] = $lieu->getVille()->getId();
            $info['rue'] = $lieu->getRue();
            $info['longitude'] = $lieu->getLongitude();
            $info['latitude'] = $lieu->getLatitude();
            $tab[]= $info;
        }
        return $this->json($tab);
    }
}
