<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
            $info['codePostal'] = $ville->getCodepostal();
            $tab[]= $info;
        }
        return $this->json($tab);
    }

    /**
     * @Route ("/villes/afficher" , name="afficher_ville")
     */
    public function afficherVille( VilleRepository $vr, Request $request){
        $villes = $vr->findAll();
        $ville = new Ville();
        $formtest = $this->createForm(VilleType::class, $ville);
        $formtest->handleRequest($request);
        if ($formtest->isSubmitted() && $formtest->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ville);
            $entityManager->flush();
            return $this->redirectToRoute('afficher_ville');
        }
        return $this->renderForm('ville/afficherVille.html.twig', compact('formtest','villes'));

    }

    /**
     * @Route ("/ville/modifier/{id}", name="ville_modifier")
     *
     */
    public function modifierVille(Ville $ville, VilleRepository $vr, EntityManagerInterface $em, Request $request ){
        $formVille = $this->createForm(VilleType::class,$ville);
        $formVille->handleRequest($request);
        if($formVille->isSubmitted()&& $formVille->isValid()){


            $em->persist($ville);
            $em->flush();
            return $this->redirectToRoute('afficher_ville');
        }
        return $this->renderForm('ville/modifierVille.html.twig',
            compact('formVille'));
    }
    /**
     * @Route ("/ville/supprimer/{id}", name="ville_supprimer")
     */
    public function supprimerVille(Ville $ville, EntityManagerInterface $em ){
        $em->remove($ville);
        $em->flush();
        return $this->redirectToRoute('afficher_ville');
    }

}
