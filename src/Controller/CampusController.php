<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CampusController extends AbstractController
{
    #[Route('/', name: 'campus_index', methods: ['GET'])]
    public function index(CampusRepository $campusRepository): Response
    {
        return $this->render('campus/index.html.twig', [
            'campuses' => $campusRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'campus_new', methods: ['GET','POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campus);
            $entityManager->flush();

            return $this->redirectToRoute('campus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campus/new.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }
    /**
     * @Route ("/campus/afficher" , name="afficher_campus")
     * @IsGranted("ROLE_ADMIN")
     */
    public function afficherCampus( CampusRepository $cr, Request $request){
        $campus1 = $cr->findAll();
        $campus = new Campus();
        $formtest = $this->createForm(CampusType::class, $campus);
        $formtest->handleRequest($request);
        if ($formtest->isSubmitted() && $formtest->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campus);
            $entityManager->flush();
            return $this->redirectToRoute('afficher_campus');
        }
        return $this->renderForm('campus/afficherCampus.html.twig', compact('formtest','campus1'));

    }

    /**
     * @Route ("/campus/modifier/{id}", name="campus_modifier")
     *@IsGranted("ROLE_ADMIN")
     */
    public function modifierVille(Campus $campus, CampusRepository $cr, EntityManagerInterface $em, Request $request ){
        $formCampus = $this->createForm(CampusType::class,$campus);
        $formCampus->handleRequest($request);
        if($formCampus->isSubmitted()&& $formCampus->isValid()){
            $em->persist($campus);
            $em->flush();
            return $this->redirectToRoute('afficher_campus');
        }
        return $this->renderForm('campus/modifierCampus.html.twig',
            compact('formCampus'));
    }

    /**
     * @Route ("/campus/supprimer/{id}", name="campus_supprimer")
     * @IsGranted("ROLE_ADMIN")
     */
    public function supprimerCampus(Campus $campus, EntityManagerInterface $em ){
        $em->remove($campus);
        $em->flush();
        return $this->redirectToRoute('afficher_campus');
    }

    /**
     * @Route ("/campus/api", name="api_campus")
     * @IsGranted("ROLE_ADMIN")
     */
    public function apiCampus(CampusRepository $cr){
        $liste = $cr->findAll();
        $tab = [];
        foreach ($liste as $campus){
            $info['id'] = $campus->getId();
            $info['nom']= $campus->getNom();
            $tab[] = $info;
        }
        return $this->json($tab);
    }

}
